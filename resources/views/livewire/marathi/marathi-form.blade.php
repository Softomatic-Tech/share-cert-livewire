<div class="mt-6 p-6 bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
    <flux:heading size="lg" class="mb-4">Marathi Data Form</flux:heading>

    <form wire:submit="save" class="space-y-4" id="marathi-form-container">
        <div>
            <flux:input 
                label="Title (Marathi)" 
                placeholder="Type in English (e.g., 'shala') and press Space..." 
                wire:model="title" 
                id="marathi-title"
                autocomplete="off"
            />
            <flux:error name="title" />
        </div>

        <div>
            <flux:textarea 
                label="Description (Marathi)" 
                placeholder="Type and press Space to convert to Marathi..." 
                wire:model="description" 
                id="marathi-description"
                rows="4"
            />
            <flux:error name="description" />
        </div>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">Save Marathi Data</flux:button>
        </div>
    </form>

    <script type="text/javascript">
        (function() {
            function initTransliteration() {
                const ids = ["marathi-title", "marathi-description"];
                
                ids.forEach(id => {
                    const el = document.getElementById(id);
                    const input = (el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA')) 
                                ? el 
                                : (el ? el.querySelector('input, textarea') : null);
                    
                    if (!input || input.dataset.transliterationInitialized) return;

                    input.addEventListener('keydown', function(e) {
                        // Trigger on Space (32) or Enter (13)
                        if (e.keyCode === 32 || e.keyCode === 13) {
                            const cursorPosition = input.selectionStart;
                            const textBeforeCursor = input.value.substring(0, cursorPosition);
                            const lastWordMatch = textBeforeCursor.match(/\b(\w+)$/);

                            if (lastWordMatch) {
                                const lastWord = lastWordMatch[1];
                                transliterateWord(lastWord, input, cursorPosition);
                            }
                        }
                    });

                    input.dataset.transliterationInitialized = "true";
                });
            }

            async function transliterateWord(word, input, cursorPosition) {
                const url = `https://inputtools.google.com/request?text=${word}&itc=mr-t-i0-und&num=1&cp=0&cs=1&ie=utf-8&oe=utf-8`;
                
                try {
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    if (data && data[0] === 'SUCCESS' && data[1][0][1][0]) {
                        const transliterated = data[1][0][1][0];
                        const textBeforeWord = input.value.substring(0, cursorPosition - word.length);
                        const textAfterWord = input.value.substring(cursorPosition);
                        
                        input.value = textBeforeWord + transliterated + textAfterWord;
                        
                        // Set cursor position after the transliterated word
                        const newCursorPos = textBeforeWord.length + transliterated.length;
                        input.setSelectionRange(newCursorPos, newCursorPos);
                        
                        // Sync with Livewire
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                } catch (error) {
                    console.error('Transliteration error:', error);
                }
            }

            // Initial load
            setTimeout(initTransliteration, 1000);

            // Livewire lifecycle support
            document.addEventListener('livewire:navigated', () => {
                setTimeout(initTransliteration, 1000);
            });
            
            // Re-run if Livewire renders (e.g. after validation or save)
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('morph.updated', (el, component) => {
                    initTransliteration();
                });
            });
        })();
    </script>
</div>
