use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up() {
Schema::create('bands', function (Blueprint $table) {
$table->id();
$table->string('name');
$table->timestamps();
});

Schema::table('users', function (Blueprint $table) {
$table->foreignId('band_id')->nullable()->constrained('bands')->onDelete('set null');
});

Schema::table('users', function (Blueprint $table) {
if (!Schema::hasColumn('users', 'type')) {
$table->enum('type', ['artist', 'manager', 'label'])->default('artist');
}
});
}

public function down() {
Schema::table('users', function (Blueprint $table) {
$table->dropForeign(['band_id']);
$table->dropColumn('band_id');
$table->dropColumn('type');
});

Schema::dropIfExists('bands');
}
};
