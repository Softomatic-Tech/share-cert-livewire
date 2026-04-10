<div>
    <div class="mb-2 w-full">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Create Society</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator variant="subtle" />
    </div>

    <div class="rounded-lg shadow-lg p-6">
        <!-- Form Section -->
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        <form wire:submit.prevent="saveSociety">
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-2">
                        <div>
                            <flux:input type="text" label="Society Name / सोसायटीचे नाव" wire:model="society_name"
                                id="society_name" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="society_name"></span>
                        </div>
                        <div>
                            <flux:input type="text" label="Registration Certificate No / नोंदणी प्रमाणपत्र क्रमांक"
                                wire:model="registration_no" id="registration_no" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="registration_no"></span>
                        </div>
                        <div>
                            <flux:input type="number" label="Total No Of Building / इमारतींची एकूण संख्या"
                                wire:model="total_building" id="total_building" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="total_building"></span>
                        </div>
                        <div>
                            <flux:input type="number" label="Total No Of Units / युनिट्सची एकूण संख्या"
                                wire:model="total_flats" id="total_flats" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="total_flats"></span>
                        </div>
                        <div>
                            <flux:textarea label="Address Line 1 / पत्ता ओळ 1" wire:model="address_1" id="address_1">
                            </flux:textarea>
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="address_1"></span>
                        </div>
                        <div>
                            <flux:textarea label="Address Line 2 / पत्ता ओळ 2" wire:model="address_2" id="address_2">
                            </flux:textarea>
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="address_2"></span>
                        </div>
                        <flux:select wire:model.live="state_id" placeholder="Choose State..." label="State / राज्य">
                            <flux:select.option value="">Choose State...</flux:select.option>
                            @foreach ($states as $st)
                                <flux:select.option value="{{ $st->id }}">{{ $st->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="city_id" placeholder="Choose City..." label="City / शहर">
                            <flux:select.option value="">Choose City...</flux:select.option>
                            @foreach ($cities as $ct)
                                <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <div>
                            <flux:input type="text" label="Pincode / पिनकोड" wire:model="pincode" id="pincode" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display" data-for="pincode"></span>
                        </div>
                        <div>
                            <flux:input type="number" label="Total No of Shares / शेअर्सची एकूण संख्या"
                                wire:model="no_of_shares" id="no_of_shares" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="no_of_shares"></span>
                        </div>
                        <div>
                            <flux:input type="number" label="Each Share Value / प्रत्येक शेअरची किंमत"
                                wire:model="share_value" id="share_value" />
                            <span class="text-sm text-blue-700 font-semibold marathi-display"
                                data-for="share_value"></span>
                        </div>
                        <div>
                            <flux:label>{{ __('Is list of signed member available?') }}</flux:label>
                            <flux:radio.group wire:model.live="is_list_of_signed_member_available" class="flex gap-4">
                                <flux:radio value="Yes" label="Yes" />
                                <flux:radio value="No" label="No" />
                            </flux:radio.group>
                        </div>
                        <div>
                            <flux:label>{{ __('Is byelaws available?') }}</flux:label>
                            <flux:radio.group wire:model="is_byelaws_available" class="flex gap-4">
                                <flux:radio value="Yes" label="Yes" />
                                <flux:radio value="No" label="No" />
                            </flux:radio.group>
                        </div>
                        {{-- <flux:input type="text" label="I Register / आय रजिस्टर" wire:model="i_register" />
                        <flux:input type="text" label="J Register / जे रजिस्टर" wire:model="j_register" /> --}}
                    </div>
                    <div class="flex justify-end mt-4">
                        <flux:button variant="primary" type="submit">Save</flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const transliterationCache = {};
        const transliterationTimeouts = {};

        async function transliterateToMarathi(text) {
            if (!text) {
                return '';
            }

            if (transliterationCache[text]) {
                return transliterationCache[text];
            }

            const url =
                `https://inputtools.google.com/request?text=${encodeURIComponent(text)}&itc=mr-t-i0-und&num=1&cp=0&cs=1&ie=utf-8&oe=utf-8`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                if (data && data[0] === 'SUCCESS' && Array.isArray(data[1])) {
                    const result = data[1]
                        .map(segment => {
                            if (Array.isArray(segment) && Array.isArray(segment[1]) && segment[1][0]) {
                                return segment[1][0];
                            }
                            return segment[0] || '';
                        })
                        .join('');

                    transliterationCache[text] = result;
                    return result;
                }
            } catch (error) {
                console.error('Marathi transliteration failed:', error);
            }

            return text;
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('input', function(e) {
                if (!e.target.matches('input[id], textarea[id]')) {
                    return;
                }

                const field = e.target.id;
                const marathiSpan = document.querySelector(`[data-for="${field}"]`);
                if (!marathiSpan) {
                    return;
                }

                const value = e.target.value;
                if (!value) {
                    marathiSpan.textContent = '';
                    return;
                }

                if (transliterationTimeouts[field]) {
                    clearTimeout(transliterationTimeouts[field]);
                }

                transliterationTimeouts[field] = setTimeout(async () => {
                    const transliterated = await transliterateToMarathi(value);
                    marathiSpan.textContent = transliterated;
                }, 250);
            });
        });
    </script>
    </section>
