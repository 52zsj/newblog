<?php
declare (strict_types=1);

namespace app\middleware;

use think\facade\Log;

class Check
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
//        Log::record($request,'info');
        Log::write($request,'info');
        return $next($request);
    }
}
