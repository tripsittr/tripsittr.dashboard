<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\{Team,User,Album,Song,Order,OrderItem,Customer,ActivityLog,Approval};
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class DemoFullActivitySeeder extends Seeder
{
    /**
     * Seed realistic cross-model activity for teams 1..7.
     */
    public function run(): void
    {
        $teamIds = [1,2,3,4,5,6,7];
        $actions = ['created','updated','deleted'];

    $songColumns = Schema::hasTable('songs') ? Schema::getColumnListing('songs') : [];

    foreach ($teamIds as $teamId) {
            $team = Team::find($teamId);
            if (! $team) { continue; }

            // Ensure a few users belong to the team
            $users = collect();
            for ($i=0;$i<rand(2,4);$i++) {
                $users->push(User::firstOrCreate([
                    'email' => "demo{$teamId}_{$i}@example.test",
                ], [
                    'name' => "Demo User {$teamId}-{$i}",
                    'password' => Hash::make('password'),
                    'type' => $i === 0 ? 'Admin' : 'Member',
                ]));
            }
            // attach team memberships
            foreach ($users as $u) { if(! $u->teams()->where('teams.id',$team->id)->exists()) { $u->teams()->attach($team->id); } }

            // Customers
            $customers = collect();
            for ($i=0;$i<rand(3,6);$i++) {
                $customers->push(Customer::create([
                    'team_id' => $teamId,
                    'name' => 'Customer '.Str::title(Str::random(6)),
                    'email' => 'customer'.$teamId.$i.'@example.test',
                    'phone' => '+1-555-'.rand(100,999).'-'.rand(1000,9999),
                    'company' => 'DemoCo '.$teamId,
                    'address_line1' => rand(100,999).' Demo St',
                    'city' => 'City'.rand(1,20),
                    'region' => 'Region'.rand(1,5),
                    'postal_code' => str_pad((string)rand(10000,99999),5,'0'),
                    'country' => 'US',
                    'notes' => 'Seeded demo customer'
                ]));
            }

            // Albums & Songs with approval workflow simulation
            $albums = collect();
            for ($i=0;$i<rand(2,5);$i++) {
                $album = Album::create([
                    'title' => "Team {$teamId} Album " . Str::upper(Str::random(5)),
                    'release_date' => now()->addDays(rand(0,20)),
                    'status' => 'draft',
                    'team_id' => $teamId,
                    'user_id' => $users->random()->id,
                ]);
                $albums->push($album);
                $this->log($teamId,'Album',$album->id,'album.created',['title'=>$album->title],$users->random()->id);

                // Simulate submission & approval/rejection
                if (rand(0,1)) {
                    $album->submitForReview($album->user_id);
                    $this->log($teamId,'Album',$album->id,'album.submitted',[], $album->user_id);
                    if (rand(0,1)) {
                        $album->approve($users->first()->id);
                        $this->log($teamId,'Album',$album->id,'album.approved',[], $users->first()->id);
                    } else {
                        $album->reject($users->first()->id,'Not a fit for catalog');
                        $this->log($teamId,'Album',$album->id,'album.rejected',['reason'=>'Not a fit for catalog'], $users->first()->id);
                    }
                }

                // Songs for album
                for ($s=0;$s<rand(3,8);$s++) {
                    $base = [
                        'title' => 'Song '.Str::title(Str::random(8)),
                        'slug' => Str::slug(Str::random(12)."-{$s}"),
                        'status' => 'draft',
                        'visibility' => 'private',
                        'distribution_status' => 'none',
                        'team_id' => $teamId,
                        'album_id' => $album->id,
                        'user_id' => $users->random()->id,
                    ];
                    if (in_array('song_file',$songColumns)) { $base['song_file'] = 'demo/'.Str::random(10).'.mp3'; }
                    if (in_array('genre',$songColumns)) { $base['genre'] = 'rock'; }
                    if (in_array('primary_artists',$songColumns)) { $base['primary_artists'] = ['Demo']; }
                    $song = Song::create($base);
                    $this->log($teamId,'Song',$song->id,'song.created',['title'=>$song->title],$song->user_id);
                }
            }

            // Orders with items
            $orderStatuses = ['draft','pending','paid','fulfilled','shipped','cancelled','refunded','partial'];
            for ($o=0;$o<rand(4,9);$o++) {
                $customer = $customers->random();
                $order = Order::create([
                    'team_id' => $teamId,
                    'customer_id' => $customer->id,
                    'reference' => Str::upper(Str::random(10)),
                    'status' => collect($orderStatuses)->random(),
                    'placed_at' => now()->subDays(rand(0,30)),
                ]);
                $this->log($teamId,'Order',$order->id,'order.created',['reference'=>$order->reference],$users->random()->id);
                // Fake items
                $itemCount = rand(1,4);
                for ($j=0;$j<$itemCount;$j++) {
                    $qty = rand(1,3);
                    $price = rand(1000,5000)/100;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'inventory_item_id' => null,
                        'catalog_item_id' => null,
                        'description' => 'Demo Item '.Str::upper(Str::random(5)),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'line_total' => $qty * $price,
                    ]);
                }
                $order->refresh(); // triggers recalculation in saving hook if needed
                $this->log($teamId,'Order',$order->id,'order.updated',['status'=>$order->status],$users->random()->id);
            }

            // Random additional activity noise
            for ($i=0;$i<rand(10,25);$i++) {
                $model = collect(['InventoryItem','Venue','Event','Knowledge'])->random();
                $id = rand(1,200);
                $act = $model .'.'. collect($actions)->random();
                $this->log($teamId,$model,$id,$act,['random'=>Str::random(5)], optional($users->random())->id);
            }
        }
    }

    protected function log($teamId,$entityType,$entityId,$action,array $changes,$userId=null): void
    {
        ActivityLog::create([
            'team_id' => $teamId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'changes' => $changes,
            'user_id' => $userId,
        ]);
    }
}
