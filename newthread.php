<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['csrf'] = $csrf_token;
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $content = $_POST['content'];

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        die('Invalid CSRF token');
    }

    // Validate input
    if (empty($subject) || empty($content)) {
        die('Subject and content are required');
    }

    if (strlen($subject) < 10 || strlen($subject) > 120) {
        die('Subject must be between 10 and 120 characters');
    }

    // Insert thread into database (pseudo code)
    insertThread($fid, $user_class->id, $subject, $content);

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

    .ql-snow .ql-editor blockquote+blockquote {
        margin-top: -5px;
    }

    .ql-fill.ql-stroke {
        stroke: white;
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

<form method="POST" onsubmit="return postThread();">
    <input type="hidden" name="fid" value="<?= $fid ?>">
    <input type="hidden" name="csrf" value="<?= $csrf_token ?>">

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

            <input type="hidden" name="content" id="quill-content">

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
            ['blockquote', 'code-block'],
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

    const icons = Quill.import('ui/icons');
    icons.blockquote = '<img src="css/images/svgs/Quote.svg" alt="Quote" class="ql-stroke ql-fill">';

    let lastQuillRange = null;
    quill.on('selection-change', function (range) {
        if (range) {
            lastQuillRange = range;
        }
    });

    const isMobile = window.innerWidth < 768;
    const pickerOptions = {
        previewPosition: isMobile ? 'none' : 'bottom',
        onEmojiSelect: function (emoji, pointerEvent) {
            const range = quill.getSelection() || lastQuillRange;
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
            'z-[1000]', 'overflow-y-auto',
            'duration-300', 'ease-in-out',
            'fixed', 'left-0', 'right-0', 'bottom-0', 'h-[40vh]', 'w-full',
            'md:absolute', 'md:top-[2rem]', 'md:left-0', 'md:w-80', 'md:rounded-xl', 'md:h-auto',
            'md:min-h-96', 'transition-all',
            'opacity-0', 'pointer-events-none', 'hidden'
        );
        emojiFormatter.appendChild(picker);
    } else {
        console.error('No .ql-formats element found!');
    }

    document.addEventListener('click', function (event) {
        // If the picker is hidden, do nothing
        if (picker.classList.contains('opacity-0')) return;

        const isClickInsidePicker = picker.contains(event.target);
        const isClickOnEmojiButton = emojiFormatter.contains(event.target);
        if (!isClickInsidePicker && !isClickOnEmojiButton) {
            toggleEmojiPicker();
        }
    });

    const toggleEmojiPicker = () => {
        const isVisible = picker.classList.contains('opacity-100');

        if (isVisible) {
            // HIDE
            picker.classList.remove('opacity-100', 'translate-y-0');
            picker.classList.add('opacity-0', 'pointer-events-none', 'translate-y-full');

            // Wait for transition to finish, then hide from layout
            setTimeout(() => {
                picker.classList.add('hidden');
            }, 300); // matches duration-300
        } else {
            // SHOW
            picker.classList.remove('hidden'); // make it enter layout again

            // Force reflow to restart animation (optional)
            void picker.offsetWidth;

            picker.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-full');
            picker.classList.add('opacity-100', 'translate-y-0');
        }
    };

    requestAnimationFrame(() => {
        picker.classList.remove('hidden');
        picker.classList.add('opacity-0', 'pointer-events-none', 'translate-y-full');
    });

    function postThread() {
        // Get Delta format
        var delta = quill.getContents();

        // Store stringified Delta into hidden input
        document.getElementById('quill-content').value = JSON.stringify(delta);

        return true;
    }
</script>

<?php include "nc_footer.php"; ?>