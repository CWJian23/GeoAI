<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeoAiAnalyzer;
use Illuminate\Http\JsonResponse;

class GeoAiController extends Controller
{
    protected GeoAiAnalyzer $analyzer;

    // 通过依赖注入引入你的 GeoAI 服务
    public function __construct(GeoAiAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * 处理选址分析请求
     */
    public function analyzeLocation(Request $request): JsonResponse
    {
        // 1. 验证前端传来的参数
        $validated = $request->validate([
            'location'      => 'required|string|max:255', // 例如: "Kajang, Selangor"
            'business_type' => 'required|string|max:100', // 例如: "cafe", "restaurant"
            'features'      => 'nullable|array',          // 前端可选传入的其他特征参数
        ]);

        try {
            // 2. 调用修改后的 Service 
            // 将 location, business_type 以及额外的特征数组(如果有) 传给 Python
            $result = $this->analyzer->analyze(
                $validated['location'],
                $validated['business_type'],
                $validated['features'] ?? []
            );

            // 3. 将 Python 返回的结果直接以 JSON 格式响应给前端
            return response()->json([
                'success' => true,
                'data'    => $result
            ]);

        } catch (\Exception $e) {
            // 4. 捕获 Python 脚本抛出的任何错误并返回友好的提示
            return response()->json([
                'success' => false,
                'message' => '分析失败: ' . $e->getMessage()
            ], 500);
        }
    }
}