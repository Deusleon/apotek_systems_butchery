<?php


namespace App;


use App\Notifications\StockNotification;
use App\Notifications\ExpiringSoonNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommonFunctions
{
    public function generateNumber()
    {
        $unique = strtoupper(substr(md5(microtime()), rand(0, 26), 8));
        return $unique;
    }

    public function sumByKey($key_name, array $new_array, $string_key)
    {
        $result = -1;
        for ($i = 0; $i < sizeof($new_array); $i++) {
            if ($new_array[$i][$string_key] == $key_name) {
                $result = $i;
                break;
            }
        }

        return $result;
    }

    public function search($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }

        return $results;
    }

    public function stockNotificationSchedule($id)
    {
        $user = User::find($id);
        if (!$user) {
            return [];
        }

        // Get current store for the user - check session first, then user store
        $store_id = session('current_store_id', $user->store_id);
        // Log::info("[CommonFunctions.php] Stock notification for user {$id}, store_id: {$store_id}");

        // Update or create stock notification for current store (now includes all alerts)
        $this->updateOrCreateNotification($user, $store_id, 'App\\Notifications\\StockNotification', 'stock');

        /*retrieve sent notification*/
        $all_notification = DB::table('notifications')
            ->where('notifiable_id', $id)
            ->where('read_at', null)
            // ->orderBy('created_at', 'desc')
            ->get();

        return json_decode($all_notification);
    }

    /**
     * Clean up old notifications, keeping only the most recent 2 of each type
     */
    private function cleanupOldNotifications($user_id, $notification_type)
    {
        $old_notifications = DB::table('notifications')
            ->where('notifiable_id', $user_id)
            ->where('type', $notification_type)
            ->where('read_at', null)
            // ->orderBy('created_at', 'desc')
            ->offset(2) // Skip the first 2 (keep them)
            ->limit(1000) // Limit to prevent too many deletions at once
            ->pluck('id');

        if ($old_notifications->isNotEmpty()) {
            DB::table('notifications')
                ->whereIn('id', $old_notifications)
                ->delete();

            // Log::info("[CommonFunctions.php] Cleaned up " . $old_notifications->count() . " old {$notification_type} notifications for user {$user_id}");
        }
    }

    /**
     * Update existing notification or create new one for the current store
     */
    private function updateOrCreateNotification($user, $store_id, $notification_type, $type_key)
    {
        // Find existing unread notification of this type for this user
        $existing_notification = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('type', $notification_type)
            ->where('read_at', null)
            ->first();

        if ($existing_notification) {
            // Check if the store_id in the notification data matches current store_id
            $notification_data = json_decode($existing_notification->data, true);
            $current_notification_store_id = $notification_data['store_id'] ?? null;

            // Only update if the store has changed or if it's been more than 30 minutes
            $needs_update = ($current_notification_store_id != $store_id) ||
                           ($existing_notification->updated_at < now()->subMinutes(30));

            if ($needs_update) {
                // Update existing notification with new store data
                $notification_class = str_replace('App\\Notifications\\', '', $notification_type);
                $notification_instance = new $notification_type($store_id);
                $new_data = $notification_instance->toArray($user);

                DB::table('notifications')
                    ->where('id', $existing_notification->id)
                    ->update([
                        'data' => json_encode($new_data), 
                        'updated_at' => now()
                    ]);

            } else {
            }
        } else {
            // Create new notification
            $notification_class = str_replace('App\\Notifications\\', '', $notification_type);
            Log::info("[CommonFunctions.php] Creating new {$notification_class} for user {$user->id} with store_id {$store_id}");
            $user->notify(new $notification_type($store_id));
        }

        // Clean up old notifications (keep only 2 of each type per user)
        $this->cleanupOldNotifications($user->id, $notification_type);
    }

}
