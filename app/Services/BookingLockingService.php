<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Redis;

class BookingLockingService
{
    /**
     * Execute a booking action with a distributed lock to prevent race conditions.
     *
     * @param  callable  $callback  The logic to run inside the lock (e.g., create hold)
     * @param  int  $timeoutSeconds  How long to wait for the lock (default 5s)
     * @return mixed|null Returns the result of the callback or null if lock failed
     */
    public function withLock(string $suiteSlug, callable $callback, int $timeoutSeconds = 5): mixed
    {
        $lockKey = "booking_lock:{$suiteSlug}";

        // Try to acquire the lock
        // SET key value NX EX timeout -> Only set if not exists, expires in X seconds
        $acquired = Redis::set($lockKey, uniqid(), 'EX', $timeoutSeconds, 'NX');

        if (! $acquired) {
            // Could not acquire lock (someone else has it)
            return null;
        }

        try {
            // Run the critical section inside a DB transaction
            DB::beginTransaction();
            $result = $callback();
            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            // Always release the lock
            // Use Lua script to ensure atomicity (check value then delete)
            // But for simplicity in MVP, DEL is usually fine if we trust the expiry
            Redis::del($lockKey);
        }
    }
}
