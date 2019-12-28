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
        if ($request->param('name') != 'think') {
            Log::debug(json_encode($request));
            Log::debug(json_encode($next));
//            return redirect('http://www.baidu.com');
        }
        return $next($request);
    }
}
