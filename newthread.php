<?php

session_start();

include 'nc_header.php';

if (!isset($_GET['fid'])) {
    header('Location: /index.php');
    exit;
}
$fid = intval($_GET['fid']);
$forums = getForums();

// Find Forum with this FID
$forum = null;
foreach ($forums as $f) {
    if ($f['id'] == $fid) {
        $forum = $f;
        break;
    }
}

if (!$forum) {
    header('Location: /index.php');
    exit;
}

$permissions = getPermissions($fid, $user_class->usergroup);
if (empty($permissions)) {
    header('Location: /index.php');
    exit;
}

$canpostthreads = (int) $permissions['canpostthreads'] == 1;
if (!$canpostthreads) {
    header('Location: /forums.php?fid=' . $fid);
    exit;
}

?>

<style>
    .ql-toolbar .ql-stroke {
        fill: none;
        stroke: #fff;
    }

    .ql-toolbar .ql-fill {
        fill: #fff;
        stroke: none;
    }

    .ql-toolbar .ql-picker {
        color: #fff;
    }

    .ql-toolbar.ql-snow {
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-sizing: border-box;
        font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        padding: 6px;
        border-radius: 8px 8px 0px 0px;
    }

    .ql-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-label {
        border-color: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
    }

    .ql-container.ql-snow {
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0px 0px 8px 8px;
    }

    .ql-editor {
        background-color:
            color-mix(in oklab, var(--color-black) 40%, transparent);
        border-radius: 0px 0px 8px 8px;
        min-height: 200px;
    }

    .ql-picker-options {
        background: black !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px;
    }

    .ql-picker-item:hover,
    .ql-picker-item.ql-selected,
    .ql-snow.ql-toolbar button:hover,
    .ql-snow .ql-toolbar button:hover,
    .ql-snow.ql-toolbar button:focus,
    .ql-snow .ql-toolbar button:focus,
    .ql-snow.ql-toolbar button.ql-active,
    .ql-snow .ql-toolbar button.ql-active,
    .ql-snow.ql-toolbar .ql-picker-label:hover,
    .ql-snow .ql-toolbar .ql-picker-label:hover,
    .ql-snow.ql-toolbar .ql-picker-label.ql-active,
    .ql-snow .ql-toolbar .ql-picker-label.ql-active,
    .ql-snow.ql-toolbar .ql-picker-item:hover,
    .ql-snow .ql-toolbar .ql-picker-item:hover,
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected,
    .ql-snow .ql-toolbar .ql-picker-item.ql-selected {
        color: red !important;
    }

    .ql-snow.ql-toolbar button:hover .ql-stroke,
    .ql-snow .ql-toolbar button:hover .ql-stroke,
    .ql-snow.ql-toolbar button:focus .ql-stroke,
    .ql-snow .ql-toolbar button:focus .ql-stroke,
    .ql-snow.ql-toolbar button.ql-active .ql-stroke,
    .ql-snow .ql-toolbar button.ql-active .ql-stroke,
    .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke,
    .ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke,
    .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke,
    .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke,
    .ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke,
    .ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke,
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke,
    .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke,
    .ql-snow.ql-toolbar button:hover .ql-stroke-miter,
    .ql-snow .ql-toolbar button:hover .ql-stroke-miter,
    .ql-snow.ql-toolbar button:focus .ql-stroke-miter,
    .ql-snow .ql-toolbar button:focus .ql-stroke-miter,
    .ql-snow.ql-toolbar button.ql-active .ql-stroke-miter,
    .ql-snow .ql-toolbar button.ql-active .ql-stroke-miter,
    .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke-miter,
    .ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke-miter,
    .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke-miter,
    .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke-miter,
    .ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke-miter,
    .ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke-miter,
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke-miter,
    .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke-miter {
        stroke: red !important;
    }

    .ql-snow.ql-toolbar button:hover .ql-fill,
    .ql-snow .ql-toolbar button:hover .ql-fill,
    .ql-snow.ql-toolbar button:focus .ql-fill,
    .ql-snow .ql-toolbar button:focus .ql-fill,
    .ql-snow.ql-toolbar button.ql-active .ql-fill,
    .ql-snow .ql-toolbar button.ql-active .ql-fill,
    .ql-snow.ql-toolbar .ql-picker-label:hover .ql-fill,
    .ql-snow .ql-toolbar .ql-picker-label:hover .ql-fill,
    .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-fill,
    .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-fill,
    .ql-snow.ql-toolbar .ql-picker-item:hover .ql-fill,
    .ql-snow .ql-toolbar .ql-picker-item:hover .ql-fill,
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-fill,
    .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-fill,
    .ql-snow.ql-toolbar button:hover .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar button:hover .ql-stroke.ql-fill,
    .ql-snow.ql-toolbar button:focus .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar button:focus .ql-stroke.ql-fill,
    .ql-snow.ql-toolbar button.ql-active .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar button.ql-active .ql-stroke.ql-fill,
    .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,
    .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke.ql-fill,
    .ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,
    .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke.ql-fill {
        fill: red !important;
    }

    #tab-toolbar {
        background-color: #080808;

    }

    #tab-panel {
        background-color: #181818;
        scrollbar-color: rgba(255, 255, 255, 0.2) #181818;
    }

    .ql-emoji::before {
        content: '';
        display: inline-block;
        background-image: url('css/images/svgs/Happy.svg');
        background-size: contain;
        width: 18px;
        height: 18px;
    }
</style>

<form method="POST">
    <input type="hidden" name="fid" value="<?= $fid ?>">

    <div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
        <div class="w-full border border-white/10 bg-black/40 border-4 rounded-lg">
            <h2 class="text-white text-xl px-4 py-2">Post new thread in <?= $forum['name'] ?></h2>

            <div class="px-4 py-2">
                <input type="text" id="subject" name="subject" placeholder="Subject"
                    class="text-white bg-black/40 border border-white/10 rounded-lg px-4 py-1 w-full" maxlength="120"
                    minlength="10" required>
            </div>

            <div class="px-4 py-2 rounded-lg text-white">
                <div id="editor"></div>
            </div>

            <div class="px-4 py-2 mb-2">
                <button type="submit"
                    class="bg-black/40 hover:bg-black/80 cursor-pointer border border-white/10 text-white py-1 px-4 rounded text-md">Post
                    thread</button>
            </div>
        </div>
    </div>
</form>

<script>
    const toolbarOptions = {
        container: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['emoji'],
            ['bold', 'italic', 'underline', 'strike'],
            ['code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'color': [] }, { 'background': [] }],
            ['clean'],
            ['link', 'image', 'video']
        ],
        handlers: {
            'emoji': function () {
                const picker = document.querySelector('em-emoji-picker');
                if (picker) {
                    toggleEmojiPicker();
                } else {
                    console.error('Emoji picker not found!');
                }
            }
        }
    };
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: toolbarOptions,
        },
    });

    const pickerOptions = {
        onEmojiSelect: function (emoji, pointerEvent) {
            const range = quill.getSelection();
            if (range) {
                quill.insertText(range.index, emoji.native);
                quill.setSelection(range.index + emoji.native.length);
            }

            toggleEmojiPicker();
        }
    }
    const picker = new EmojiMart.Picker(pickerOptions)

    // Find second .ql-formats element and append Picker inside it
    const emojiFormatter = document.querySelector('.ql-formats:nth-of-type(2)');
    if (emojiFormatter) {
        emojiFormatter.classList.add('relative');
        picker.classList.add(
            'absolute', 'z-[1000]', 'top-[2rem]', 'left-0',
            'w-80', 'md:w-96', 'max-w-full',
            'md:rounded-xl', 'transition-all', 'duration-300', 'ease-in-out',
            'opacity-0', 'pointer-events-none', 'translate-y-full',
            'md:top-[2rem]', 'md:left-0',
            'fixed', 'bottom-0', 'left-0', 'right-0', 'md:absolute',
            'md:bottom-auto', 'md:right-auto', 'h-[60vh]', 'md:h-auto',
            'overflow-y-auto'
        );
        emojiFormatter.appendChild(picker);
    } else {
        console.error('No .ql-formats element found!');
    }

    document.addEventListener('click', function (event) {
        // If the picker is hidden, do nothing
        if (picker.classList.contains('hidden')) return;

        const isClickInsidePicker = picker.contains(event.target);
        const isClickOnEmojiButton = emojiFormatter.contains(event.target);
        if (!isClickInsidePicker && !isClickOnEmojiButton) {
            toggleEmojiPicker();
        }
    });

    const toggleEmojiPicker = () => {
        const isVisible = picker.classList.contains('opacity-100');

        if (isVisible) {
            picker.classList.remove('opacity-100', 'translate-y-0');
            picker.classList.add('opacity-0', 'pointer-events-none', 'translate-y-full');
        } else {
            picker.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-full');
            picker.classList.add('opacity-100', 'translate-y-0');
        }
    };
</script>

<?php include "nc_footer.php"; ?>