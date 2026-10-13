<x-layout
    title="Encounters"
    description="Browse D&D encounters created by the community and find inspiration for your next session."
>
    <h1>Encounters</h1>

    <x-encounter-card
        :id="1"
        title="Goblin Ambush"
        type="Combat"
        difficulty="Medium"
        creator="Toon"
    />

    <x-encounter-card
        :id="2"
        title="Haunted Tavern"
        type="Social"
        difficulty="Hard"
        creator="Dungeon Master"
    />
</x-layout>