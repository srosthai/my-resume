# Audio Files for Music Player

This directory contains the audio files used by the MusicPlayer component.

## Adding Music Files

1. **Add your MP3 files** to this directory with the following names:
   - `background-music-1.mp3`
   - `background-music-2.mp3`
   - `background-music-3.mp3`

2. **File Requirements:**
   - Format: MP3 (recommended for best browser compatibility)
   - Size: Keep files under 10MB for better loading performance
   - Quality: 128kbps - 320kbps recommended

3. **Update the Music Library:**
   To add more songs or change the existing ones, edit the `musicLibrary` array in `/resources/js/components/MusicPlayer.vue`

## Example File Structure:
```
public/audio/
├── README.md
├── background-music-1.mp3
├── background-music-2.mp3
└── background-music-3.mp3
```

## Free Music Resources:
- **Freesound.org** - Free sound effects and music
- **Zapsplat** - Free music and sound effects (requires registration)
- **YouTube Audio Library** - Free music from YouTube
- **Incompetech** - Free music by Kevin MacLeod
- **Pixabay Music** - Free background music

## Important Notes:
- Make sure you have proper licensing for any music you use
- The music player will automatically skip tracks that fail to load
- Music files should be optimized for web use (compressed but good quality)
- Consider adding a variety of music styles to match different sections of your portfolio

## Customization:
You can customize the music player by editing the `MusicPlayer.vue` component:
- Change track information (title, artist)
- Modify the player appearance
- Add new controls or features
- Update the music library with your preferred tracks