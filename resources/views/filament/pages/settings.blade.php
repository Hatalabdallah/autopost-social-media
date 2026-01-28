<div>
    <x-filament-panels::page x-data="{ activeTab: 'twitter' }">
        <x-filament::tabs>
            {{-- Twitter (x.com) --}}
            <x-filament::tabs.item alpine-active="activeTab === 'twitter'" x-on:click="activeTab = 'twitter'">
                Twitter (x.com)
            </x-filament::tabs.item>
            {{-- Facebook --}}
            <x-filament::tabs.item alpine-active="activeTab === 'facebook'" x-on:click="activeTab = 'facebook'">
                Facebook
            </x-filament::tabs.item>

            {{-- LinkedIn --}}
            <x-filament::tabs.item alpine-active="activeTab === 'linkedin'" x-on:click="activeTab = 'linkedin'">
                LinkedIn
            </x-filament::tabs.item>

            {{-- Instagram --}}
            <x-filament::tabs.item alpine-active="activeTab === 'instagram'" x-on:click="activeTab = 'instagram'">
                Instagram
            </x-filament::tabs.item>

            {{-- TikTok --}}
            <x-filament::tabs.item alpine-active="activeTab === 'tiktok'" x-on:click="activeTab = 'tiktok'">
                TikTok
            </x-filament::tabs.item>

            {{-- WhatsApp --}}
            <x-filament::tabs.item alpine-active="activeTab === 'whatsapp'" x-on:click="activeTab = 'whatsapp'">
                WhatsApp
            </x-filament::tabs.item>
            
        </x-filament::tabs>

        {{-- Twitter (x.com) --}}
        <div x-show="activeTab === 'twitter'">
            <x-filament-panels::form wire:submit="saveTwitter">
                {{-- Twitter Form --}}
                {{ $this->twitterForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getTwitterActions()" />
            </x-filament-panels::form>
        </div>

        {{-- Facebook --}}
        <div x-show="activeTab === 'facebook'">
            <x-filament-panels::form wire:submit="saveFacebook">
                {{-- Facebook Form --}}
                {{ $this->facebookForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getFacebookActions()" />
            </x-filament-panels::form>
        </div>

        {{-- LinkedIn --}}
        <div x-show="activeTab === 'linkedin'">
            <x-filament-panels::form wire:submit="saveLinkedin">
                {{-- LinkedIn Form --}}
                {{ $this->linkedinForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getLinkedinActions()" />
            </x-filament-panels::form>
        </div>

        {{-- Instagram --}}
        <div x-show="activeTab === 'instagram'">
            <x-filament-panels::form wire:submit="saveInstagram">
                {{-- Instagram Form --}}
                {{ $this->instagramForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getInstagramActions()" />
            </x-filament-panels::form>
        </div>

        {{-- TikTok --}}
        <div x-show="activeTab === 'tiktok'">
            <x-filament-panels::form wire:submit="saveTiktok">
                {{-- TikTok Form --}}
                {{ $this->tiktokForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getTiktokActions()" />
            </x-filament-panels::form>
        </div>

        {{-- WhatsApp --}}
        <div x-show="activeTab === 'whatsapp'">
            <x-filament-panels::form wire:submit="saveWhatsapp">
                {{-- WhatsApp Form --}}
                {{ $this->whatsappForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getWhatsappActions()" />
            </x-filament-panels::form>
        </div>

        <x-filament-actions::modals />
    </x-filament-panels::page>
</div>
