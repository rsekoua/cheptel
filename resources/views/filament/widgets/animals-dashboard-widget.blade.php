@php
    $data = $this->getAnimalsData();
@endphp

<x-filament-widgets::widget>
    <a href="{{ $data['url'] }}" class="block">
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 20px; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -40px; right: 40px; width: 80px; height: 80px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>

            <div style="position: relative; z-index: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9; margin: 0;">Animaux</p>
                        <p style="font-size: 2.5rem; font-weight: 800; margin: 4px 0 0 0; line-height: 1;">{{ $data['total'] }}</p>
                    </div>
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px;">
                        <x-heroicon-s-heart style="width: 24px; height: 24px; color: white;" />
                    </div>
                </div>

                <div style="display: flex; gap: 16px; margin-top: 16px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <div style="width: 8px; height: 8px; background: #fbbf24; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; opacity: 0.9;">{{ $data['truies'] }} Truies</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <div style="width: 8px; height: 8px; background: #38bdf8; border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; opacity: 0.9;">{{ $data['verrats'] }} Verrats</span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</x-filament-widgets::widget>
