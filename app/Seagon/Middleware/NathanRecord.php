<?php

namespace App\Seagon\Middleware;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NathanRecord
{
    private Repository $cache;

    public function __construct()
    {
        $this->cache = Cache::store();
    }

    public function __invoke(Request $request, Closure $next)
    {
        $nathanId = env('NATHAN_ID');
        $seagonGroup = env('SEAGON_GROUP');

        // Nathan 出聲過後，讓 AI 陪 seagon 在 seagon group 聊 30 分鐘
        if ($this->isNathan($request, $nathanId) && $this->isSeagonGroup($request, $seagonGroup)) {
            $this->cache->put('nathan_said', true, 1800);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @param mixed $nathanId
     * @return bool
     */
    private function isNathan(Request $request, string $nathanId): bool
    {
        return $nathanId === $request->input('events.0.source.userId');
    }

    /**
     * @param Request $request
     * @param string $seagonGroup
     * @return bool
     */
    private function isSeagonGroup(Request $request, string $seagonGroup): bool
    {
        return $seagonGroup === $request->input('events.0.source.groupId');
    }
}
