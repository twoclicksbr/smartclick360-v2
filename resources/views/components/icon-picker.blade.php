@props(['name' => 'icon', 'value' => ''])

@php
    $iconValue = $value ?? '';
@endphp

{{-- Input hidden para o formulário --}}
<input type="hidden" name="{{ $name }}" id="{{ $name }}_hidden" value="{{ $iconValue }}">

{{-- Input group com preview + botões --}}
<div class="input-group input-group-solid">
    <span class="input-group-text d-flex align-items-center justify-content-center" style="min-width: 50px;" id="{{ $name }}_preview">
        @if($iconValue)
            <i class="{{ $iconValue }} fs-3"></i>
        @else
            <i class="ki-outline ki-question fs-3 text-muted"></i>
        @endif
    </span>
    @if($iconValue)
    <button type="button" class="btn btn-icon btn-light-danger" id="{{ $name }}_clear" title="Limpar">
        <i class="ki-outline ki-cross fs-4"></i>
    </button>
    @endif
    <button type="button" class="btn btn-icon btn-light-primary" id="{{ $name }}_open_modal" title="Selecionar ícone">
        <i class="ki-outline ki-magnifier fs-4"></i>
    </button>
</div>

{{-- Modal de seleção --}}
<div class="modal fade" id="iconPickerModal_{{ $name }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            {{-- Header --}}
            <div class="modal-header">
                <h2 class="modal-title">Selecionar Ícone</h2>
                <input type="text" class="form-control form-control-sm w-300px ms-5" id="{{ $name }}_search" placeholder="Buscar ícone...">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>

            {{-- Style toggle --}}
            <div class="modal-body pt-5">
                <div class="btn-group w-100 mb-5" role="group">
                    <input type="radio" class="btn-check" name="{{ $name }}_style" id="{{ $name }}_outline" value="ki-outline" checked>
                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="{{ $name }}_outline">Outline</label>

                    <input type="radio" class="btn-check" name="{{ $name }}_style" id="{{ $name }}_duotone" value="ki-duotone">
                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="{{ $name }}_duotone">Duotone</label>

                    <input type="radio" class="btn-check" name="{{ $name }}_style" id="{{ $name }}_solid" value="ki-solid">
                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary" for="{{ $name }}_solid">Solid</label>
                </div>

                {{-- Grid de ícones --}}
                <div id="{{ $name }}_grid" class="row g-3" style="overflow-y: auto; max-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Fix: z-index para modais aninhados */
[id^="iconPickerModal_"] {
    z-index: 1065;
}
[id^="iconPickerModal_"] + .modal-backdrop,
.modal-backdrop ~ .modal-backdrop {
    z-index: 1060;
}

.icon-picker-item {
    padding: 0.75rem;
    text-align: center;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.icon-picker-item:hover {
    background-color: var(--bs-light-primary);
}

.icon-picker-item.selected {
    background-color: var(--bs-light-primary);
    border-color: var(--bs-primary);
}

.icon-picker-item i {
    display: block;
    margin-bottom: 0.5rem;
}

.icon-picker-item i.ki-duotone {
    position: relative;
    display: inline-block;
    width: 1em;
    height: 1em;
    line-height: 1;
}

.icon-picker-item i.ki-duotone span[class^="path"] {
    position: absolute;
    left: 0;
    top: 0;
}

/* Hover effect on icon preview */
[id$="_preview"] {
    cursor: pointer;
    transition: transform 0.2s ease;
}

[id$="_preview"]:hover {
    transform: scale(2.5);
    z-index: 10;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    "use strict";

    // Fix: modais aninhados no Bootstrap 5
    document.addEventListener('show.bs.modal', function(event) {
        // Se o modal sendo aberto é um icon picker
        if (event.target.id && event.target.id.startsWith('iconPickerModal_')) {
            // Encontra o modal pai que está aberto
            const parentModal = document.querySelector('.modal.show');
            if (parentModal && parentModal.id !== event.target.id) {
                // Salva referência ao modal pai
                event.target.dataset.parentModal = parentModal.id;
                // Impede que o Bootstrap feche o modal pai
                parentModal.style.overflow = 'hidden';
            }
        }
    });

    document.addEventListener('hidden.bs.modal', function(event) {
        // Quando um icon picker fecha
        if (event.target.id && event.target.id.startsWith('iconPickerModal_')) {
            const parentModalId = event.target.dataset.parentModal;
            if (parentModalId) {
                const parentModal = document.getElementById(parentModalId);
                if (parentModal) {
                    // Reabre o modal pai e restaura o scroll
                    parentModal.style.overflow = '';
                    // Garante que o body mantenha a classe modal-open
                    document.body.classList.add('modal-open');
                    // Restaura o backdrop se necessário
                    if (!document.querySelector('.modal-backdrop')) {
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                }
            }
        }
    });

    // Ícones KTIcons organizados por categoria (573 ícones)
    const iconCategories = [
        {
            name: 'Abstract',
            icons: ['abstract', 'abstract-1', 'abstract-2', 'abstract-3', 'abstract-4', 'abstract-5', 'abstract-6', 'abstract-7', 'abstract-8', 'abstract-9', 'abstract-10', 'abstract-11', 'abstract-12', 'abstract-13', 'abstract-14', 'abstract-15', 'abstract-16', 'abstract-17', 'abstract-18', 'abstract-19', 'abstract-20', 'abstract-21', 'abstract-22', 'abstract-23', 'abstract-24', 'abstract-25', 'abstract-26', 'abstract-27', 'abstract-28', 'abstract-29', 'abstract-30', 'abstract-31', 'abstract-32', 'abstract-33', 'abstract-34', 'abstract-35', 'abstract-36', 'abstract-37', 'abstract-38', 'abstract-39', 'abstract-40', 'abstract-41', 'abstract-42', 'abstract-43', 'abstract-44', 'abstract-45', 'abstract-46', 'abstract-47', 'abstract-48', 'abstract-49']
        },
        {
            name: 'Arrows & Directions',
            icons: ['arrow-circle-left', 'arrow-circle-right', 'arrow-diagonal', 'arrow-down', 'arrow-down-left', 'arrow-down-refraction', 'arrow-down-right', 'arrow-left', 'arrow-mix', 'arrow-right', 'arrow-right-left', 'arrows-circle', 'arrows-loop', 'arrow-two-diagonals', 'arrow-up', 'arrow-up-down', 'arrow-up-left', 'arrow-up-refraction', 'arrow-up-right', 'arrow-zigzag', 'black-down', 'black-left', 'black-left-line', 'black-right', 'black-right-line', 'black-up', 'double-down', 'double-left', 'double-left-arrow', 'double-right', 'double-right-arrow', 'double-up', 'down', 'down-square', 'entrance-left', 'entrance-right', 'exit-down', 'exit-left', 'exit-right', 'exit-right-corner', 'exit-up', 'left', 'left-square', 'right', 'right-left', 'right-square', 'to-left', 'to-right', 'up', 'up-down', 'up-square']
        },
        {
            name: 'Files & Folders',
            icons: ['add-files', 'add-folder', 'archive', 'archive-tick', 'delete-files', 'delete-folder', 'document', 'file', 'file-added', 'file-deleted', 'file-down', 'file-left', 'file-right', 'file-sheet', 'files-tablet', 'file-up', 'folder', 'folder-added', 'folder-down', 'folder-up', 'some-files', 'update-file', 'update-folder']
        },
        {
            name: 'Communication',
            icons: ['call', 'message-add', 'message-edit', 'message-minus', 'message-notif', 'message-programming', 'message-question', 'messages', 'message-text', 'message-text-2', 'notification', 'notification-2', 'notification-bing', 'notification-circle', 'notification-favorite', 'notification-on', 'notification-status', 'phone', 'send', 'sms', 'telephone-geolocation', 'whatsapp']
        },
        {
            name: 'Social Media',
            icons: ['behance', 'classmates', 'discord', 'dribbble', 'facebook', 'github', 'instagram', 'slack', 'snapchat', 'social-media', 'spotify', 'tiktok', 'trello', 'twitch', 'twitter', 'youtube']
        },
        {
            name: 'Technology & Coding',
            icons: ['android', 'angular', 'apple', 'bootstrap', 'chrome', 'code', 'css', 'figma', 'html', 'illustrator', 'js', 'js-2', 'laravel', 'microsoft', 'photoshop', 'python', 'react', 'spring-framework', 'ts', 'vue', 'vuesax', 'xd', 'xaomi', 'yii']
        },
        {
            name: 'Business & Finance',
            icons: ['award', 'bank', 'bill', 'briefcase', 'brifecase-cros', 'brifecase-tick', 'brifecase-timer', 'chart', 'chart-line', 'chart-line-down', 'chart-line-down-2', 'chart-line-star', 'chart-line-up', 'chart-line-up-2', 'chart-pie-3', 'chart-pie-4', 'chart-pie-simple', 'chart-pie-too', 'chart-simple', 'chart-simple-2', 'chart-simple-3', 'cheque', 'credit-cart', 'dollar', 'euro', 'finance-calculator', 'financial-schedule', 'graph', 'graph-2', 'graph-3', 'graph-4', 'graph-up', 'office-bag', 'percentage', 'purchase', 'ranking', 'receipt-square', 'two-credit-cart', 'wallet']
        },
        {
            name: 'Cryptocurrency',
            icons: ['avalanche', 'binance', 'binance-usd', 'bitcoin', 'enjin-coin', 'nexo', 'wanchain', 'xmr']
        },
        {
            name: 'Navigation & Location',
            icons: ['burger-menu', 'burger-menu-1', 'burger-menu-2', 'burger-menu-3', 'burger-menu-4', 'burger-menu-5', 'burger-menu-6', 'compass', 'geolocation', 'geolocation-home', 'home', 'home-1', 'home-2', 'home-3', 'map', 'menu', 'route', 'safe-home']
        },
        {
            name: 'Time & Calendar',
            icons: ['calendar', 'calendar-2', 'calendar-8', 'calendar-add', 'calendar-edit', 'calendar-remove', 'calendar-search', 'calendar-tick', 'electronic-clock', 'time', 'timer', 'watch']
        },
        {
            name: 'Users & People',
            icons: ['people', 'profile-circle', 'profile-user', 'security-user', 'teacher', 'user', 'user-edit', 'user-square', 'user-tick']
        },
        {
            name: 'Security',
            icons: ['faceid', 'fingerprint-scanning', 'key', 'key-square', 'lock', 'lock-2', 'lock-3', 'password-check', 'security-check', 'shield', 'shield-cross', 'shield-search', 'shield-slash', 'shield-tick', 'verify']
        },
        {
            name: 'E-commerce & Shopping',
            icons: ['barcode', 'basket', 'basket-ok', 'discount', 'handcart', 'lots-shopping', 'price-tag', 'scan-barcode', 'shop']
        },
        {
            name: 'Delivery & Logistics',
            icons: ['courier', 'courier-express', 'delivery', 'delivery-2', 'delivery-24', 'delivery-3', 'delivery-door', 'delivery-geolocation', 'delivery-time', 'logistic', 'package', 'parcel', 'parcel-tracking']
        },
        {
            name: 'Devices & Technology',
            icons: ['airpod', 'bluetooth', 'devices', 'devices-2', 'external-drive', 'keyboard', 'laptop', 'monitor-mobile', 'mouse', 'mouse-circle', 'mouse-square', 'general-mouse', 'router', 'screen', 'simcard', 'simcard-2', 'tablet', 'tablet-book', 'tablet-delete', 'tablet-down', 'tablet-ok', 'tablet-text-down', 'tablet-text-up', 'tablet-up', 'tech-wifi', 'wifi', 'wifi-home', 'wifi-square']
        },
        {
            name: 'Design & Creative',
            icons: ['brush', 'bucket', 'bucket-square', 'colors-square', 'color-swatch', 'design', 'design-2', 'design-frame', 'design-mask', 'eraser', 'frame', 'mask', 'paintbucket', 'pencil']
        },
        {
            name: 'Media & Entertainment',
            icons: ['cd', 'dj', 'joystick', 'picture', 'speaker']
        },
        {
            name: 'General UI & Actions',
            icons: ['add-item', 'add-notepad', 'bookmark', 'bookmark-2', 'check', 'check-circle', 'check-square', 'click', 'clipboard', 'copy', 'copy-success', 'cross', 'cross-circle', 'cross-square', 'dash', 'double-check', 'double-check-circle', 'eye', 'eye-slash', 'filter', 'filter-edit', 'filter-search', 'filter-square', 'filter-tablet', 'filter-tick', 'flag', 'focus', 'gear', 'information', 'information-2', 'information-3', 'information-4', 'information-5', 'loading', 'magnifier', 'maximize', 'minus', 'minus-circle', 'minus-folder', 'minus-square', 'more-2', 'plus', 'plus-circle', 'plus-square', 'question', 'question-2', 'questionnaire-tablet', 'search-list', 'setting', 'setting-2', 'setting-3', 'setting-4', 'share', 'star', 'switch', 'toggle-off', 'toggle-off-circle', 'toggle-on', 'toggle-on-circle', 'trash', 'trash-square']
        },
        {
            name: 'Transport & Vehicles',
            icons: ['airplane', 'airplane-square', 'bus', 'car', 'car-2', 'car-3', 'rocket', 'satellite', 'scooter', 'scooter-2', 'ship', 'trailer', 'truck']
        },
        {
            name: 'Medical & Health',
            icons: ['bandage', 'capsule', 'pill', 'pulse', 'syringe', 'test-tubes']
        },
        {
            name: 'Elements & Shapes',
            icons: ['cube-2', 'cube-3', 'diamonds', 'element-1', 'element-2', 'element-3', 'element-4', 'element-5', 'element-6', 'element-7', 'element-8', 'element-9', 'element-10', 'element-11', 'element-12', 'element-equal', 'element-plus', 'triangle']
        },
        {
            name: 'Text & Typography',
            icons: ['square-brackets', 'subtitle', 'text', 'text-align-center', 'text-align-justify-center', 'text-align-left', 'text-align-right', 'text-bold', 'text-circle', 'text-italic', 'text-number', 'text-strikethrough', 'text-underline', 'underlining']
        },
        {
            name: 'Weather & Nature',
            icons: ['drop', 'feather', 'flash-circle', 'moon', 'night-day', 'ocean', 'sun', 'thermometer', 'tree']
        },
        {
            name: 'Food & Drinks',
            icons: ['coffee', 'cup', 'milk']
        },
        {
            name: 'Miscellaneous',
            icons: ['address-book', 'artificial-intelligence', 'auto-brightness', 'badge', 'book', 'book-open', 'book-square', 'calculator', 'category', 'celsius', 'cloud', 'cloud-add', 'cloud-change', 'cloud-download', 'crown', 'crown-2', 'data', 'directbox-default', 'disconnect', 'disk', 'dislike', 'dots-circle', 'dots-circle-vertical', 'dots-horizontal', 'dots-square', 'dots-square-vertical', 'dots-vertical', 'dropbox', 'educare', 'electricity', 'emoji-happy', 'fasten', 'fat-rows', 'ghost', 'gift', 'glass', 'google', 'google-play', 'grid', 'grid-2', 'grid-frame', 'happy-emoji', 'heart', 'heart-circle', 'icon', 'kanban', 'like', 'like-2', 'like-folder', 'like-shapes', 'like-tag', 'lovely', 'lts', 'medal-star', 'note', 'note-2', 'notepad', 'notepad-bookmark', 'notepad-edit', 'pails', 'paper-clip', 'paypal', 'pin', 'pointers', 'printer', 'rescue', 'row-horizontal', 'row-vertical', 'save-2', 'save-deposit', 'scroll', 'size', 'slider', 'slider-horizontal', 'slider-horizontal-2', 'slider-vertical', 'slider-vertical-2', 'soft', 'soft-2', 'soft-3', 'sort', 'status', 'support-24', 'tag', 'tag-cross', 'technology', 'technology-2', 'technology-3', 'technology-4', 'theta', 'vibe', 'virus', 'wrench']
        }
    ];

    // Mapa de paths duotone (quantidade de spans necessários por ícone)
    const duotonePaths = {"abstract":3,"abstract-1":3,"abstract-10":3,"abstract-11":3,"abstract-12":3,"abstract-13":3,"abstract-14":3,"abstract-15":3,"abstract-16":3,"abstract-17":3,"abstract-18":3,"abstract-19":3,"abstract-2":3,"abstract-20":3,"abstract-21":3,"abstract-22":3,"abstract-23":3,"abstract-24":3,"abstract-25":3,"abstract-26":3,"abstract-27":3,"abstract-28":3,"abstract-29":3,"abstract-3":3,"abstract-30":3,"abstract-31":3,"abstract-32":3,"abstract-33":3,"abstract-34":3,"abstract-35":3,"abstract-36":3,"abstract-37":3,"abstract-38":3,"abstract-39":3,"abstract-4":3,"abstract-40":3,"abstract-41":3,"abstract-42":3,"abstract-43":3,"abstract-44":3,"abstract-45":3,"abstract-46":3,"abstract-47":3,"abstract-48":4,"abstract-49":4,"abstract-5":3,"abstract-6":1,"abstract-7":3,"abstract-8":3,"abstract-9":3,"add-files":4,"add-folder":3,"add-item":4,"add-notepad":5,"address-book":4,"airplane":3,"airplane-square":3,"airpod":4,"android":7,"angular":4,"apple":3,"archive":4,"archive-tick":3,"arrow-circle-left":3,"arrow-circle-right":3,"arrow-diagonal":4,"arrow-down":3,"arrow-down-left":3,"arrow-down-refraction":3,"arrow-down-right":3,"arrow-left":3,"arrow-mix":3,"arrow-right":3,"arrow-right-left":3,"arrows-circle":3,"arrows-loop":3,"arrow-two-diagonals":6,"arrow-up":3,"arrow-up-down":3,"arrow-up-left":3,"arrow-up-refraction":3,"arrow-up-right":3,"arrow-zigzag":3,"artificial-intelligence":9,"auto-brightness":4,"avalanche":3,"award":4,"badge":6,"bandage":3,"bank":3,"barcode":9,"basket":5,"basket-ok":5,"behance":3,"bill":7,"binance":6,"binance-usd":5,"bitcoin":3,"black-down":1,"black-left":1,"black-left-line":3,"black-right":1,"black-right-line":3,"black-up":1,"bluetooth":3,"book":5,"bookmark":3,"bookmark-2":3,"book-open":5,"book-square":4,"bootstrap":4,"briefcase":3,"brifecase-cros":4,"brifecase-tick":4,"brifecase-timer":4,"brush":3,"bucket":5,"bucket-square":4,"burger-menu":5,"burger-menu-1":5,"burger-menu-2":11,"burger-menu-3":10,"burger-menu-4":1,"burger-menu-5":1,"burger-menu-6":1,"bus":6,"calculator":7,"calendar":3,"calendar-2":6,"calendar-8":7,"calendar-add":7,"calendar-edit":4,"calendar-remove":7,"calendar-search":5,"calendar-tick":7,"call":9,"capsule":3,"car":6,"car-2":7,"car-3":4,"category":5,"cd":3,"celsius":3,"chart":3,"chart-line":3,"chart-line-down":3,"chart-line-down-2":4,"chart-line-star":4,"chart-line-up":3,"chart-line-up-2":3,"chart-pie-3":4,"chart-pie-4":4,"chart-pie-simple":3,"chart-pie-too":3,"chart-simple":5,"chart-simple-2":5,"chart-simple-3":5,"check":1,"check-circle":3,"check-square":3,"cheque":8,"chrome":3,"classmates":3,"click":6,"clipboard":4,"cloud":1,"cloud-add":3,"cloud-change":4,"cloud-download":3,"code":5,"coffee":7,"colors-square":5,"color-swatch":22,"compass":3,"copy":1,"copy-success":3,"courier":4,"courier-express":8,"credit-cart":3,"cross":3,"cross-circle":3,"cross-square":3,"crown":3,"crown-2":4,"css":3,"cube-2":4,"cube-3":3,"cup":3,"dash":3,"data":6,"delete-files":3,"delete-folder":3,"delivery":6,"delivery-2":10,"delivery-24":5,"delivery-3":4,"delivery-door":5,"delivery-geolocation":6,"delivery-time":6,"design":3,"design-2":3,"design-frame":3,"design-mask":3,"devices":6,"devices-2":4,"diamonds":3,"directbox-default":5,"disconnect":6,"discount":3,"disk":3,"dislike":3,"dj":1,"document":3,"dollar":4,"dots-circle":5,"dots-circle-vertical":5,"dots-horizontal":4,"dots-square":5,"dots-square-vertical":5,"dots-vertical":4,"double-check":3,"double-check-circle":4,"double-down":4,"double-left":3,"double-left-arrow":3,"double-right":3,"double-right-arrow":3,"double-up":4,"down":1,"down-square":3,"dribbble":7,"drop":3,"dropbox":6,"educare":5,"electricity":11,"electronic-clock":5,"element-1":5,"element-10":4,"element-11":5,"element-12":4,"element-2":3,"element-3":3,"element-4":3,"element-5":3,"element-6":3,"element-7":3,"element-8":3,"element-9":3,"element-equal":6,"element-plus":6,"emoji-happy":5,"enjin-coin":3,"entrance-left":3,"entrance-right":3,"eraser":4,"euro":4,"exit-down":3,"exit-left":3,"exit-right":3,"exit-right-corner":3,"exit-up":3,"external-drive":6,"eye":4,"eye-slash":5,"facebook":3,"faceid":7,"fasten":3,"fat-rows":3,"feather":3,"figma":6,"file":3,"file-added":3,"file-deleted":3,"file-down":3,"file-left":3,"file-right":3,"file-sheet":3,"files-tablet":3,"file-up":3,"filter":3,"filter-edit":3,"filter-search":4,"filter-square":3,"filter-tablet":3,"filter-tick":3,"finance-calculator":8,"financial-schedule":5,"fingerprint-scanning":6,"flag":3,"flash-circle":3,"flask":3,"focus":3,"folder":3,"folder-added":3,"folder-down":3,"folder-up":3,"frame":5,"gear":3,"general-mouse":3,"geolocation":3,"geolocation-home":3,"ghost":4,"gift":5,"github":3,"glass":4,"google":3,"google-play":3,"graph":5,"graph-2":4,"graph-3":3,"graph-4":3,"graph-up":7,"grid":3,"grid-2":3,"grid-frame":4,"handcart":1,"happy-emoji":3,"heart":3,"heart-circle":3,"home":1,"home-1":3,"home-2":3,"home-3":3,"html":3,"icon":4,"illustrator":5,"information":4,"information-2":4,"information-3":4,"information-4":4,"information-5":4,"instagram":3,"joystick":5,"js":3,"js-2":3,"kanban":3,"key":3,"keyboard":3,"key-square":3,"laptop":3,"laravel":8,"left":1,"left-square":3,"like":3,"like-2":3,"like-folder":3,"like-shapes":3,"like-tag":3,"loading":3,"lock":4,"lock-2":6,"lock-3":4,"logistic":8,"lots-shopping":9,"lovely":3,"lts":3,"magnifier":3,"map":4,"mask":4,"maximize":6,"medal-star":5,"menu":5,"message-add":4,"message-edit":3,"message-minus":3,"message-notif":6,"message-programming":5,"message-question":4,"messages":6,"message-text":4,"message-text-2":4,"microsoft":5,"milk":4,"minus":1,"minus-circle":3,"minus-folder":3,"minus-square":3,"monitor-mobile":3,"moon":3,"more-2":5,"mouse":3,"mouse-circle":3,"mouse-square":3,"nexo":3,"night-day":11,"note":3,"note-2":5,"notepad":6,"notepad-bookmark":7,"notepad-edit":3,"notification":4,"notification-2":3,"notification-bing":4,"notification-circle":3,"notification-favorite":4,"notification-on":6,"notification-status":5,"ocean":20,"office-bag":5,"package":4,"pails":10,"paintbucket":4,"paper-clip":1,"parcel":6,"parcel-tracking":4,"password-check":6,"paypal":3,"pencil":3,"people":6,"percentage":3,"phone":3,"photoshop":3,"picture":3,"pill":1,"pin":3,"plus":1,"plus-circle":3,"plus-square":4,"pointers":4,"price-tag":4,"printer":6,"profile-circle":4,"profile-user":5,"pulse":3,"purchase":3,"python":3,"question":4,"question-2":4,"questionnaire-tablet":3,"ranking":5,"react":3,"receipt-square":3,"rescue":3,"right":1,"right-left":4,"right-square":3,"rocket":3,"route":5,"router":3,"row-horizontal":3,"row-vertical":3,"safe-home":3,"satellite":7,"save-2":3,"save-deposit":5,"scan-barcode":9,"scooter":8,"scooter-2":1,"screen":5,"scroll":4,"search-list":4,"security-check":5,"security-user":3,"send":3,"setting":3,"setting-2":3,"setting-3":6,"setting-4":1,"share":7,"shield":3,"shield-cross":4,"shield-search":4,"shield-slash":4,"shield-tick":3,"ship":4,"shop":6,"simcard":6,"simcard-2":3,"size":3,"slack":9,"slider":5,"slider-horizontal":4,"slider-horizontal-2":4,"slider-vertical":4,"slider-vertical-2":4,"sms":3,"snapchat":3,"social-media":3,"soft":7,"soft-2":3,"soft-3":3,"some-files":3,"sort":5,"speaker":4,"spotify":3,"spring-framework":1,"square-brackets":5,"star":1,"status":4,"subtitle":6,"sun":10,"support-24":4,"switch":3,"syringe":4,"tablet":4,"tablet-book":3,"tablet-delete":4,"tablet-down":4,"tablet-ok":4,"tablet-text-down":5,"tablet-text-up":3,"tablet-up":4,"tag":4,"tag-cross":4,"teacher":3,"technology":10,"technology-2":3,"technology-3":5,"technology-4":8,"tech-wifi":3,"telephone-geolocation":4,"test-tubes":3,"text":1,"text-align-center":5,"text-align-justify-center":5,"text-align-left":5,"text-align-right":5,"text-bold":4,"text-circle":7,"text-italic":5,"text-number":7,"text-strikethrough":4,"text-underline":4,"thermometer":3,"theta":3,"tiktok":3,"time":3,"timer":4,"toggle-off":3,"toggle-off-circle":3,"toggle-on":3,"toggle-on-circle":3,"to-left":1,"to-right":1,"trailer":6,"trash":6,"trash-square":5,"tree":4,"trello":4,"triangle":4,"truck":6,"ts":4,"twitch":4,"twitter":3,"two-credit-cart":6,"underlining":4,"up":1,"update-file":5,"update-folder":3,"up-down":4,"up-square":3,"user":3,"user-edit":4,"user-square":4,"user-tick":4,"verify":3,"vibe":3,"virus":4,"vue":3,"vuesax":4,"wallet":5,"wanchain":3,"watch":3,"whatsapp":3,"wifi":5,"wifi-home":5,"wifi-square":5,"wrench":3,"xaomi":3,"xd":4,"xmr":3,"yii":4,"youtube":3};

    let currentStyle = 'ki-outline';
    let searchTimeout;
    let iconsRendered = false;

    const modal = document.getElementById('iconPickerModal_{{ $name }}');
    const grid = document.getElementById('{{ $name }}_grid');
    const searchInput = document.getElementById('{{ $name }}_search');
    const hiddenInput = document.getElementById('{{ $name }}_hidden');
    const preview = document.getElementById('{{ $name }}_preview');
    const clearBtn = document.getElementById('{{ $name }}_clear');
    const openModalBtn = document.getElementById('{{ $name }}_open_modal');

    // Abrir modal via JavaScript (sem backdrop se houver modal pai)
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            const parentModal = document.querySelector('.modal.show');
            const iconPickerModal = new bootstrap.Modal(modal, {
                backdrop: parentModal ? false : true
            });
            iconPickerModal.show();
        });
    }

    // Initialize preview for duotone icons on page load
    function initializePreview() {
        const currentValue = hiddenInput.value;
        if (currentValue && currentValue.startsWith('ki-duotone')) {
            const iconName = currentValue.split(' ')[1].replace('ki-', '');
            const pathCount = duotonePaths[iconName] || 2;

            let spans = '';
            for (let p = 1; p <= pathCount; p++) {
                spans += `<span class="path${p}"></span>`;
            }

            const iconElement = preview.querySelector('i');
            if (iconElement) {
                iconElement.innerHTML = spans;
            }
        }
    }

    // Run on page load
    initializePreview();

    // Renderizar ícones (lazy load)
    modal.addEventListener('shown.bs.modal', function() {
        if (!iconsRendered) {
            renderIcons();
            iconsRendered = true;
        }
    });

    // Mudança de estilo
    document.querySelectorAll('input[name="{{ $name }}_style"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentStyle = this.value;
            renderIcons(searchInput.value);
        });
    });

    // Busca com debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            renderIcons(this.value);
        }, 200);
    });

    // Limpar seleção
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            hiddenInput.value = '';
            preview.innerHTML = '<i class="ki-outline ki-question fs-3 text-muted"></i>';
            this.remove();
        });
    }

    function renderIcons(filter = '') {
        grid.innerHTML = '';

        // Iterar por categorias
        iconCategories.forEach(category => {
            // Filtrar ícones da categoria
            const filteredIcons = category.icons.filter(icon =>
                filter === '' || icon.toLowerCase().includes(filter.toLowerCase())
            );

            // Se a categoria não tem ícones após o filtro, pula
            if (filteredIcons.length === 0) {
                return;
            }

            // Criar título da categoria
            const categoryTitle = document.createElement('div');
            categoryTitle.className = 'col-12';
            const titleH6 = document.createElement('h6');
            titleH6.className = `text-muted fw-semibold mb-3 ${grid.children.length > 0 ? 'mt-5' : ''}`;
            titleH6.textContent = category.name;
            categoryTitle.appendChild(titleH6);
            grid.appendChild(categoryTitle);

            // Renderizar ícones da categoria
            filteredIcons.forEach(iconName => {
                const fullClass = `${currentStyle} ki-${iconName}`;
                const isSelected = hiddenInput.value === fullClass;

                // Criar elementos
                const col = document.createElement('div');
                col.className = 'col-2';

                const card = document.createElement('div');
                card.className = `icon-picker-item ${isSelected ? 'selected' : ''}`;

                const icon = document.createElement('i');
                icon.className = `${fullClass} fs-3x`;

                // Add spans for duotone icons
                if (currentStyle === 'ki-duotone') {
                    const pathCount = duotonePaths[iconName] || 2;
                    for (let p = 1; p <= pathCount; p++) {
                        const span = document.createElement('span');
                        span.className = `path${p}`;
                        icon.appendChild(span);
                    }
                }

                const name = document.createElement('div');
                name.className = 'text-muted fs-8 text-truncate';
                name.textContent = iconName;

                card.appendChild(icon);
                card.appendChild(name);
                col.appendChild(card);

                // Click handler
                card.addEventListener('click', function() {
                    selectIcon(fullClass);
                });

                grid.appendChild(col);
            });
        });
    }

    function selectIcon(iconClass) {
        // Atualizar valores
        hiddenInput.value = iconClass;

        // Create icon element with spans if duotone
        let iconHtml;
        if (iconClass.startsWith('ki-duotone')) {
            // Extract icon name from class (format: "ki-duotone ki-{name}")
            const iconName = iconClass.split(' ')[1].replace('ki-', '');
            const pathCount = duotonePaths[iconName] || 2;

            let spans = '';
            for (let p = 1; p <= pathCount; p++) {
                spans += `<span class="path${p}"></span>`;
            }
            iconHtml = `<i class="${iconClass} fs-3">${spans}</i>`;
        } else {
            iconHtml = `<i class="${iconClass} fs-3"></i>`;
        }

        preview.innerHTML = iconHtml;

        // Adicionar botão de limpar se não existir
        if (!document.getElementById('{{ $name }}_clear')) {
            const inputGroup = preview.parentElement;
            const newClearBtn = document.createElement('button');
            newClearBtn.type = 'button';
            newClearBtn.className = 'btn btn-icon btn-light-danger';
            newClearBtn.id = '{{ $name }}_clear';
            newClearBtn.title = 'Limpar';
            newClearBtn.innerHTML = '<i class="ki-outline ki-cross fs-4"></i>';
            newClearBtn.addEventListener('click', function() {
                hiddenInput.value = '';
                preview.innerHTML = '<i class="ki-outline ki-question fs-3 text-muted"></i>';
                this.remove();
            });

            // Inserir o botão de limpar antes do botão de selecionar
            const selectBtn = document.getElementById('{{ $name }}_open_modal');
            inputGroup.insertBefore(newClearBtn, selectBtn);
        }

        // Fechar modal
        const modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();
    }
})();
</script>
@endpush
