<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ShareVenueModal extends Component {
    public $venue;

    public function __construct($venue) {
        $this->venue = $venue;
    }

    public function render() {
        return view('components.share-venue-modal');
    }
}
