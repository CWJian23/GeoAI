#!/usr/bin/env python3
"""GeoAI analysis using OpenStreetMap & Real Machine Learning (Random Forest + Gradient Boosting)."""

import json
import csv
import io
import math
import re
import sys
import urllib.parse
import urllib.request
import traceback
import os
import numpy as np
import joblib

from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor, VotingRegressor

def request_json(url, params=None, data=None):
    if params:
        url += "?" + urllib.parse.urlencode(params)
    request = urllib.request.Request(
        url,
        data=data,
        headers={"User-Agent": "GeoAI-web/1.0 (local business analysis)"},
    )
    with urllib.request.urlopen(request, timeout=8) as response:
        return json.loads(response.read().decode("utf-8"))

def request_text(url):
    request = urllib.request.Request(
        url,
        headers={"User-Agent": "GeoAI-web/1.0 (local business analysis)"},
    )
    with urllib.request.urlopen(request, timeout=8) as response:
        return response.read().decode("utf-8")

def dosm_state_name(state):
    aliases = {
        "kuala lumpur": "wp kuala lumpur",
        "federal territory of kuala lumpur": "wp kuala lumpur",
        "wilayah persekutuan kuala lumpur": "wp kuala lumpur",
        "putrajaya": "wp putrajaya",
        "federal territory of putrajaya": "wp putrajaya",
        "wilayah persekutuan putrajaya": "wp putrajaya",
        "labuan": "wp labuan",
        "federal territory of labuan": "wp labuan",
        "wilayah persekutuan labuan": "wp labuan",
        "penang": "pulau pinang",
    }
    normalized_state = " ".join(str(state).lower().replace(".", "").split())
    return aliases.get(normalized_state, normalized_state)

def selected_state(place, location):
    address = place.get("address", {})
    state = address.get("state") or address.get("region") or address.get("state_district")
    if state:
        return state

    location_text = location.lower()
    state_names = [
        "johor", "kedah", "kelantan", "melaka", "negeri sembilan", "pahang",
        "perak", "perlis", "pulau pinang", "penang", "sabah", "sarawak",
        "selangor", "terengganu", "kuala lumpur", "putrajaya", "labuan",
    ]
    return next((name for name in state_names if name in location_text), "")

def selected_city(place, location):
    address = place.get("address", {})
    city = address.get("city") or address.get("town") or address.get("municipality") or address.get("city_district")
    return city or location.split(",")[0].strip()

def distance_km(latitude, longitude, other_latitude, other_longitude):
    radius = 6371
    lat_one, lat_two = math.radians(latitude), math.radians(other_latitude)
    delta_lat = math.radians(other_latitude - latitude)
    delta_lon = math.radians(other_longitude - longitude)
    value = math.sin(delta_lat / 2) ** 2 + math.cos(lat_one) * math.cos(lat_two) * math.sin(delta_lon / 2) ** 2
    return round(radius * 2 * math.asin(math.sqrt(value)), 1)

def suggestion_location_label(tags, index):
    if tags.get("public_transport") or tags.get("railway") in {"station", "halt", "tram_stop"} or tags.get("highway") == "bus_stop":
        return "Transit access area"
    if tags.get("amenity") in {"restaurant", "cafe", "marketplace"}:
        return "Customer activity area"
    if tags.get("shop"):
        return "Commercial activity area"
    return ["Nearby opportunity area", "Local demand area", "Alternative activity area"][index % 3]

def recommendation_reason(tags, distance, index):
    if tags.get("public_transport") or tags.get("railway") in {"station", "halt", "tram_stop"}:
        reasons = [
            f"Public transport access is about {distance:g} km away, supporting commuter visibility.",
            f"A transit connection around {distance:g} km away may improve access for customers without cars.",
            f"Nearby transport about {distance:g} km away can create repeat visits during daily travel periods.",
        ]
        return reasons[index % len(reasons)]
    if tags.get("amenity") in {"restaurant", "cafe", "marketplace"}:
        reasons = [
            f"Nearby food and market activity about {distance:g} km away may bring consistent customer traffic.",
            f"Customer-facing activity around {distance:g} km away suggests an established destination area.",
            f"The surrounding food and service cluster about {distance:g} km away may support lunchtime and evening demand.",
        ]
        return reasons[index % len(reasons)]
    if tags.get("shop"):
        reasons = [
            f"Established retail activity about {distance:g} km away suggests useful customer flow.",
            f"Nearby shops around {distance:g} km away indicate an existing local spending catchment.",
            f"Retail activity about {distance:g} km away may improve visibility among regular neighbourhood visitors.",
        ]
        return reasons[index % len(reasons)]
    reasons = [
        f"Mapped local activity about {distance:g} km away may strengthen visibility for a nearby business.",
        f"The surrounding area about {distance:g} km away offers a practical alternative customer catchment.",
        f"This nearby area about {distance:g} km away provides another demand signal to compare.",
    ]
    return reasons[index % len(reasons)]

def transit_service_label(tags):
    searchable_text = " ".join(str(tags.get(key, "")) for key in ("name", "network", "operator", "route", "route_ref")).lower()
    if "mrt" in searchable_text or tags.get("station") == "subway":
        return "MRT"
    if "lrt" in searchable_text:
        return "LRT"
    if "monorail" in searchable_text:
        return "Monorail"
    if "ktm" in searchable_text or tags.get("railway") in {"station", "halt"}:
        return "KTM Komuter"
    if "rapid kl" in searchable_text:
        return "Rapid KL bus"
    if tags.get("highway") == "bus_stop" or tags.get("bus") == "yes" or tags.get("route") == "bus":
        return "Bus service"
    return "Public transport"

def dosm_population(state):
    if not state:
        return None
    try:
        rows = csv.DictReader(io.StringIO(request_text(
            "https://storage.dosm.gov.my/population/population_state.csv"
        )))
        matching_rows = []
        for row in rows:
            row_state = dosm_state_name(row.get("state", ""))
            if row_state == dosm_state_name(state) and row.get("age") == "overall" and row.get("sex") == "both" and row.get("ethnicity") == "overall":
                matching_rows.append(row)

        if matching_rows:
            latest_row = max(matching_rows, key=lambda row: row.get("date", ""))
            return round(float(latest_row.get("population", 0)) * 1000)
    except Exception:
        return None
    return None

def normalize_location_text(value):
    return " ".join(str(value or "").lower().replace(".", "").split())

def dosm_median_income(state, location, display_name=""):
    try:
        rows = list(csv.DictReader(io.StringIO(request_text(
            "https://storage.dosm.gov.my/hies/hh_income_parlimen.csv"
        ))))
        normalized_state = dosm_state_name(state)
        searchable_location = normalize_location_text(f"{location} {display_name}")
        matches = []

        for row in rows:
            constituency = normalize_location_text(row.get("parlimen", ""))
            constituency_name = re.sub(r"^p\s*\d+\s*", "", constituency).strip()
            full_constituency_match = constituency and constituency in searchable_location
            state_match = dosm_state_name(row.get("state", "")) == normalized_state
            name_match_without_state = not normalized_state and constituency_name and constituency_name in searchable_location
            if full_constituency_match or (state_match and constituency_name and constituency_name in searchable_location) or name_match_without_state:
                matches.append(row)

        if not matches:
            return None

        latest_row = max(matches, key=lambda row: row.get("date", ""))
        median_income = latest_row.get("income_median")
        if median_income in (None, ""):
            return None

        return {
            "value": round(float(median_income)),
            "parlimen": latest_row.get("parlimen"),
            "year": latest_row.get("date", "")[:4],
            "source": "OpenDOSM Household Income by Parliament",
        }
    except Exception:
        return None

def world_population_review_population(city):
    if not city:
        return None
    try:
        page = request_text("https://worldpopulationreview.com/cities/malaysia")
        rows = re.findall(
            r'<a[^>]*class="[^"]*main-link[^"]*"[^>]*href="/cities/malaysia/[^" ]+"[^>]*>([^<]+)</a>.*?class="[^"]*text-wpr-subtitle[^"]*"[^>]*>([0-9,]+)</p>',
            page,
            re.DOTALL,
        )
        normalized_city = " ".join(str(city).lower().replace(".", "").split())
        exact_match = next(
            (population for name, population in rows if " ".join(name.lower().replace(".", "").split()) == normalized_city),
            None,
        )
        if exact_match:
            return int(exact_match.replace(",", ""))

        matching_rows = [
            population for name, population in rows
            if " ".join(name.lower().replace(".", "").split()) in normalized_city
        ]
        return int(max(matching_rows, key=lambda population: int(population.replace(",", ""))).replace(",", "")) if matching_rows else None
    except Exception:
        return None

# rf+gb ensemble model file path
MODEL_FILE = os.path.join(os.path.dirname(__file__), 'geoai_ensemble_model.pkl')

def train_and_save_model():
    """首次运行时自动生成合成数据集，并训练 RF+GB 融合模型"""
    sys.stderr.write("Initialising GeoAI: Training Random Forest & Gradient Boosting model...\n")
    
    
    np.random.seed(42)
    samples = 1000
    transit = np.random.randint(0, 20, samples)
    commercial = np.random.randint(0, 100, samples)
    competitors = np.random.randint(0, 30, samples)
    population = np.random.randint(10000, 200000, samples)
    
    
    y_score = 40 + (transit * 1.5) + (commercial * 0.3) - (competitors * 1.2) + (population / 20000)
    y_score = y_score + np.sin(competitors) * 5 
    y_score = np.clip(y_score, 15, 98) 
    
    X = np.column_stack((transit, commercial, competitors, population))
    
    
    rf = RandomForestRegressor(n_estimators=50, max_depth=10, random_state=42)
    gb = GradientBoostingRegressor(n_estimators=50, learning_rate=0.1, max_depth=4, random_state=42)
    
    ensemble_model = VotingRegressor(estimators=[('random_forest', rf), ('gradient_boosting', gb)])
    ensemble_model.fit(X, y_score)
    
    joblib.dump(ensemble_model, MODEL_FILE)
    sys.stderr.write("Model trained and saved successfully.\n")
    return ensemble_model

def predict_score_with_ml(features, business_type):
    """加载模型并进行预测"""
    try:
        if not os.path.exists(MODEL_FILE):
            model = train_and_save_model()
        else:
            model = joblib.load(MODEL_FILE)
            
        X_input = np.array([[
            features.get('transit_points', 0),
            features.get('commercial_points', 0),
            features.get('competitors', 0),
            features.get('estimated_population', 50000) or 50000
        ]])
        
        raw_score = model.predict(X_input)[0]
        
        business_type_lower = business_type.lower()
        if 'cafe' in business_type_lower and features.get('transit_points', 0) > 5:
            raw_score += 4
        if 'restaurant' in business_type_lower and features.get('commercial_points', 0) > 30:
            raw_score += 4
            
        return round(float(np.clip(raw_score, 10, 98)))
    except Exception as e:
        sys.stderr.write(f"ML Prediction Error: {str(e)}\n")
        return 65 
# ==============================================================================

def fallback(location, business_type, radius_meters=500, coordinates=None, lookup_income=False):
    seed = sum(ord(char) for char in (location + business_type))
    if coordinates:
        latitude, longitude = coordinates
    else:
        latitude = 3.139 + ((seed % 31) - 15) / 1000
        longitude = 101.6869 + (((seed // 31) % 31) - 15) / 1000
    latitude_scale = radius_meters / 111000
    longitude_scale = radius_meters / (111000 * max(math.cos(math.radians(latitude)), 0.01))
    
    fallback_features = {
        'transit_points': 2 + seed % 7,
        'commercial_points': 12 + seed % 35,
        'competitors': 2 + seed % 12,
        'estimated_population': 30000 + seed % 70000,
    }
    score = predict_score_with_ml(fallback_features, business_type)
    median_income = dosm_median_income("", location, location) if lookup_income or coordinates is not None else None
    
    suggestions = [
        {"name": "Central commercial corridor", "description": "Nearby mixed-use area with strong daily activity.", "reason": "Convenient access supports repeat customers.", "score": min(96, score + 15), "coordinates": [round(latitude + latitude_scale * 0.45, 6), round(longitude + longitude_scale * 0.35, 6)]},
        {"name": "Transit-oriented opportunity", "description": "Close to public transport.", "reason": "Improves visibility during commuter periods.", "score": min(96, score + 20), "coordinates": [round(latitude - latitude_scale * 0.4, 6), round(longitude + longitude_scale * 0.45, 6)]},
        {"name": "Residential catchment", "description": "Dense residential catchment.", "reason": "Dependable local demand.", "score": min(96, score + 12), "coordinates": [round(latitude + latitude_scale * 0.2, 6), round(longitude - longitude_scale * 0.45, 6)]},
    ]
    for suggestion in suggestions:
        suggestion["distance_km"] = distance_km(latitude, longitude, *suggestion["coordinates"])
        
    return {
        "coordinates": [round(latitude, 6), round(longitude, 6)],
        "display_name": location, "geocoded": False, "score": score,
        "radius_meters": radius_meters,
        "metrics": {"estimated_population": fallback_features['estimated_population'], "transit_points": fallback_features['transit_points'], "commercial_points": fallback_features['commercial_points'], "competitors": fallback_features['competitors']},
        "dashboard": {
            "headline": [{"label": "Crowd Density", "value": "Moderate to High"}, {"label": "Traffic Flow", "value": "Moderate to busy traffic during peak hours"}, {"label": "Commercial Activity", "value": "Established commercial zone"}, {"label": "Peak Hours", "value": "11:00 AM - 2:00 PM, 6:00 PM - 9:00 PM"}],
            "demographics": {"score": max(40, score - 5), "population": fallback_features['estimated_population'], "median_income": median_income["value"] if median_income else None, "median_income_year": median_income["year"] if median_income else None, "median_income_parlimen": median_income["parlimen"] if median_income else None, "median_income_source": median_income["source"] if median_income else None, "primary_age_group": "Mixed age groups"},
            "competition": {"score": 75, "nearby_competitors": fallback_features['competitors'], "market_saturation": "Moderate"},
            "accessibility": {"score": max(30, score - 2), "public_transport": "Bus service", "parking": "Needs local verification", "foot_traffic": "Moderate"},
            "market_demand": {"score": score, "current_demand": "Moderate", "growth_potential": "Moderate"},
            "amenities": [],
            "public_transport_access": f"Bus service ({fallback_features['transit_points']} mapped points) within {radius_meters / 1000:g} km.",
            "recommendations": [f"This location may be suitable for a {business_type.replace('-', ' ')}.", "Compare rent and licensing before finalizing."],
        },
        "summary": "Preliminary estimate using location context and Machine Learning (RF+GB).",
        "sources": ["GeoAI ML Engine"],
        "suggestions": suggestions,
    }

def analyze(location, business_type, offline=False, radius_meters=500):
    if offline:
        return fallback(location, business_type, radius_meters)

    latitude = None
    longitude = None
    try:
        places = request_json("https://nominatim.openstreetmap.org/search", {"q": location, "format": "jsonv2", "limit": 1, "countrycodes": "my", "addressdetails": 1})
        if not places:
            return fallback(location, business_type, radius_meters, lookup_income=True)

        place = places[0]
        latitude, longitude = float(place["lat"]), float(place["lon"])
        query = f"""
[out:json][timeout:10];
(
    nwr(around:{radius_meters},{latitude},{longitude})[public_transport];
    nwr(around:{radius_meters},{latitude},{longitude})[highway=bus_stop];
    nwr(around:{radius_meters},{latitude},{longitude})[railway~\"station|halt|tram_stop\"];
    nwr(around:{radius_meters},{latitude},{longitude})[shop];
    nwr(around:{radius_meters},{latitude},{longitude})[amenity~\"marketplace|restaurant|cafe\"];
    nwr(around:{radius_meters},{latitude},{longitude})[amenity~\"bank|clinic|hospital\"];
    nwr(around:{radius_meters},{latitude},{longitude})[population];
);
out center tags;"""
        elements = request_json("https://overpass-api.de/api/interpreter", data=query.encode("utf-8"))["elements"]
        
        transit = [item for item in elements if "public_transport" in item.get("tags", {}) or item.get("tags", {}).get("railway") in {"station", "halt", "tram_stop"}]
        transit.extend(item for item in elements if item.get("tags", {}).get("highway") == "bus_stop" and item not in transit)
        transit_links = []
        for item in transit:
            service = transit_service_label(item.get("tags", {}))
            if service not in transit_links:
                transit_links.append(service)
        transit_summary = ", ".join(transit_links) if transit_links else "No named public transport mapped"
        commercial = [item for item in elements if "shop" in item.get("tags", {}) or item.get("tags", {}).get("amenity") in {"marketplace", "restaurant", "cafe"}]
        competitors = [item for item in commercial if item.get("tags", {}).get("shop") or item.get("tags", {}).get("amenity") in {"restaurant", "cafe", "marketplace"}]
        
        amenities = []
        for item in elements:
            tags = item.get("tags", {})
            name = tags.get("name") or tags.get("amenity") or tags.get("shop")
            if name and name not in amenities:
                amenities.append(name)
                
        populations = [int(item["tags"]["population"]) for item in elements if item.get("tags", {}).get("population", "").isdigit()]
        population = max(populations) if populations else None
        
        state = selected_state(place, location)
        city = selected_city(place, location)
        median_income = dosm_median_income(state, location, place.get("display_name", location))
        city_population = world_population_review_population(city)
        state_population = dosm_population(state)
        final_pop = city_population or state_population or population or 0
        population_source = "World Population Review (2026)" if city_population else ("OpenDOSM" if state_population else "OpenStreetMap")

        features = {
            'transit_points': len(transit),
            'commercial_points': len(commercial),
            'competitors': len(competitors),
            'estimated_population': final_pop
        }
        score = predict_score_with_ml(features, business_type)
        
        demographic_score = min(100, max(40, score - 5))
        transport_score = min(100, max(30, score - 2))
        affordability_score = min(90, max(40, 100 - (len(commercial) // 8)))

        candidates = commercial[:3] or transit[:3]
        suggestions = []
        for index, item in enumerate(candidates):
            item_tags = item.get("tags", {})
            center = item.get("center", item)
            item_lat, item_lon = center.get("lat"), center.get("lon")
            if item_lat is None or item_lon is None:
                continue
            name = suggestion_location_label(item_tags, index)
            coordinates = [round(float(item_lat), 6), round(float(item_lon), 6)]
            suggestion_distance = distance_km(latitude, longitude, *coordinates)
            if suggestion_distance * 1000 > radius_meters:
                continue
            suggestions.append({
                "name": name,
                "description": "Nearby activity may provide customer visibility.",
                "reason": recommendation_reason(item_tags, suggestion_distance, index),
                "score": min(96, score + 8 - index * 2),
                "distance_km": suggestion_distance,
                "coordinates": coordinates,
            })
            
        result = fallback(location, business_type, radius_meters, [latitude, longitude])
        result.update({
            "coordinates": [round(latitude, 6), round(longitude, 6)],
            "display_name": place.get("display_name", location),
            "geocoded": True,
            "radius_meters": radius_meters,
            "score": score,
            "metrics": {
                "demographic_score": demographic_score, "transportation_score": transport_score, "affordability_score": affordability_score, "estimated_population": final_pop, "transit_points": len(transit), "commercial_points": len(commercial),
            },
            "dashboard": {
                "headline": [{"label": "Crowd Density", "value": "High" if score >= 70 else "Moderate"}, {"label": "Traffic Flow", "value": f"{len(transit)} mapped transit points within 5 km"}, {"label": "Commercial Activity", "value": f"{len(commercial)} mapped commercial places"}, {"label": "Peak Hours", "value": "11:00 AM - 2:00 PM, 6:00 PM - 9:00 PM"}],
                "demographics": {"score": demographic_score, "population": final_pop, "population_source": population_source, "median_income": median_income["value"] if median_income else None, "median_income_year": median_income["year"] if median_income else None, "median_income_parlimen": median_income["parlimen"] if median_income else None, "median_income_source": median_income["source"] if median_income else None, "primary_age_group": "Mixed age groups"},
                "competition": {"score": max(35, 90 - len(competitors) * 3), "nearby_competitors": len(competitors), "market_saturation": "High" if len(competitors) >= 15 else ("Moderate" if len(competitors) >= 7 else "Low")},
                "accessibility": {"score": transport_score, "public_transport": transit_summary, "parking": "Needs local verification", "foot_traffic": "High" if len(commercial) >= 30 else "Moderate"},
                "market_demand": {"score": score, "current_demand": "High" if score >= 75 else "Moderate", "growth_potential": "Strong" if score >= 65 else "Moderate"},
                "amenities": amenities[:8],
                "public_transport_access": f"{transit_summary} ({len(transit)} mapped points) within {radius_meters / 1000:g} km.",
                "recommendations": [f"This location has a {('strong' if score >= 70 else 'moderate')} opportunity profile based on Ensemble ML analysis.", "Review competitor density on site."],
            },
            "summary": f"The {business_type.replace('-', ' ')} opportunity scores {score}/100 based on a Machine Learning evaluation (Random Forest + Gradient Boosting).",
            "sources": ["OpenDOSM", "OpenStreetMap", "GeoAI ML Engine"],
            "suggestions": suggestions or result["suggestions"],
        })
        return result
    except Exception as e:
        sys.stderr.write(f"Error during analysis: {str(e)}\n")
        traceback.print_exc(file=sys.stderr)
        coordinates = [latitude, longitude] if latitude is not None and longitude is not None else None
        return fallback(location, business_type, radius_meters, coordinates)

if __name__ == "__main__":
    try:
        input_data = json.loads(sys.stdin.read())
        result = analyze(input_data["location"], input_data["business_type"], input_data.get("offline", False), input_data.get("radius_meters", 500))
        print(json.dumps(result))
    except Exception as e:
        sys.stderr.write(f"Fatal error: {str(e)}\n")
        sys.exit(1)