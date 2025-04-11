<x-filament::page>
    <form method="POST" action="{{ route('filament.pages.auth.register-team') }}">
        @csrf
        <div>
            <label for="team_name">Team Name</label>
            <input id="team_name" name="team_name" type="text" required>
        </div>
        <div>
            <label for="team_type">Team Type</label>
            <select id="team_type" name="team_type" required>
                <option value="Admin">Admin</option>
                <option value="Band">Band</option>
                <option value="Solo Artist">Solo Artist</option>
                <option value="Management">Management</option>
                <option value="Record Label">Record Label</option>
            </select>
        </div>
        <div>
            <button type="submit">Register Team</button>
        </div>
    </form>
</x-filament::page>
