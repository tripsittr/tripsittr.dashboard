# Custom Audio Player Component

A clean, custom audio player component for Filament with a slim, clickable progress bar designed to match your site's theme.

## Features

- 🎮 **Full Player Controls**: Play/pause, skip forward/backward, volume, and speed controls
- 🖱️ **Interactive Seeking**: Click the progress bar to seek to any position
- ⌨️ **Keyboard Shortcuts**: Space (play/pause), arrows (seek/volume), M (mute)
- 🎨 **Theme Integration**: Supports light/dark mode and custom colors
- 📱 **Responsive Design**: Works on desktop and mobile devices
- ♿ **Accessibility**: ARIA labels, keyboard navigation, focus management
- 🔧 **Highly Configurable**: Enable/disable features and customize appearance

## Basic Usage

```php
use App\Infolists\Components\AudioEntry;

AudioEntry::make('audio_url')
    ->label('Audio Recording')
```

## Advanced Configuration

```php
AudioEntry::make('audio_url')
    ->label('Custom Audio Player')
    ->defaultVolume(0.8)                    // Set initial volume (0.0-1.0)
    ->defaultSpeed(1.25)                    // Set initial playback speed
    ->primaryColor('#10b981')               // Custom primary color
    ->backgroundColor('#f3f4f6')            // Custom background color
    ->autoplay(false)                       // Enable autoplay (usually blocked)
    ->controls(true)                        // Show/hide all controls
    ->timeDisplay(true)                     // Show/hide time display
    ->volumeControl(true)                   // Show/hide volume control
    ->speedControl(true)                    // Show/hide speed control
    ->format('mp3')                         // Audio format hint
```

## Available Methods

### Display Options
- `controls(bool $show = true)` - Show/hide player controls
- `timeDisplay(bool $show = true)` - Show/hide time display
- `volumeControl(bool $show = true)` - Show/hide volume control
- `speedControl(bool $show = true)` - Show/hide speed control

### Behavior Options
- `autoplay(bool $autoplay = true)` - Enable autoplay (note: most browsers block this)
- `defaultVolume(float $volume)` - Set default volume (0.0 to 1.0)
- `defaultSpeed(float $speed)` - Set default playback speed
- `format(string $format)` - Set audio format hint

### Appearance Options
- `primaryColor(string $color)` - Set primary color (hex, rgb, etc.)
- `backgroundColor(string $color)` - Set background color

## Keyboard Shortcuts

When the audio player has focus:
- **Space**: Play/pause toggle
- **Left Arrow**: Skip backward 10 seconds
- **Right Arrow**: Skip forward 30 seconds
- **Up Arrow**: Increase volume
- **Down Arrow**: Decrease volume
- **M**: Toggle mute

## Supported Audio Formats

The component supports all HTML5 audio formats:
- MP3 (.mp3)
- WAV (.wav)
- OGG (.ogg)
- FLAC (.flac)
- AAC (.aac)
- M4A (.m4a)
- WMA (.wma)

## Browser Compatibility

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Examples

### Minimal Player
```php
AudioEntry::make('audio_url')
    ->label('Simple Audio')
    ->speedControl(false)
    ->volumeControl(false)
```

### Podcast Player
```php
AudioEntry::make('podcast_url')
    ->label('Podcast Episode')
    ->defaultSpeed(1.25)
    ->primaryColor('#f59e0b') // amber-500
```

### Music Player
```php
AudioEntry::make('music_url')
    ->label('Music Track')
    ->defaultVolume(0.7)
    ->primaryColor('#ec4899') // pink-500
    ->backgroundColor('#fdf2f8') // pink-50
```

## Troubleshooting

### Audio Won't Play
1. Check that the audio URL is accessible
2. Verify the audio format is supported
3. Check browser console for CORS errors
4. Ensure HTTPS for external audio files

### Progress/Controls Not Working
1. Verify that JavaScript is enabled
2. Check for console errors
3. Ensure the Blade view was recompiled (php artisan view:clear)

### Styling Issues
1. Ensure Tailwind CSS is properly loaded
2. Check for CSS conflicts
3. Verify dark mode configuration

## Security Considerations

- Always validate audio URLs server-side
- Use HTTPS for external audio files
- Consider implementing audio file upload restrictions
- Be aware of CORS policies for cross-origin audio

## Performance Notes

- Lean DOM updates for smooth progress rendering
- Component is optimized for multiple instances on the same page
- Event listeners are properly cleaned up to prevent memory leaks