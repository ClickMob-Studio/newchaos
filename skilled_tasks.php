<?php

include_once 'header.php';

$unlockedSkills = [];
if (!empty($user_class->skills)) {
    $unlockedSkills = array_map('strval', $user_class->skills);
} else {
    claim_specialization(1, $user_class->id);
    $user_class = new User($user_class->id);
    $unlockedSkills = array_map('strval', $user_class->skills);
}
?>

<style>
    .task-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin: auto;
        background-color: rgb(23, 25, 23);
        padding: 20px;
        border-radius: 10px;
        /* Awesome gradient border */
        border: 2px solid rgb(16, 16, 16);
        background-clip: padding-box, border-box;
        background-origin: border-box, padding-box;
    }

    .task-card {
        padding: 12;
        border-radius: 8px;
        border: 2px solid #333;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .task-card:hover {
        background: rgb(41, 38, 38);
    }

    .task-card h3 {
        margin: 0 0 6px;
        font-size: 16px;
    }

    .task-card p {
        margin: 0;
        font-size: 12px;
        color: #aaa;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .modal-content {
        background: #1e1e1e;
        padding: 24px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        position: relative;
    }

    .modal-content h3 {
        margin-top: 0;
    }

    .close-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        color: #aaa;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }

    .start-button {
        background: #28a745;
        color: white;
        padding: 10px 16px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 12px;
    }

    .start-button:hover {
        background: #218838;
    }
</style>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script>
    if (typeof window.Popper === 'undefined' && typeof window.PopperCore !== 'undefined') {
        window.Popper = window.PopperCore;
    }

    window.tippy = window.tippy || window['tippy'];
</script>

<!-- Tippy + Popper -->
<!-- <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/shift-away.css" />
<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/dark.css">
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.min.js"></script> -->

<!-- Cytoscape and Extensions -->
<!-- <script src="https://unpkg.com/cytoscape@3.28.0/dist/cytoscape.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cytoscape-popper@1.0.7/cytoscape-popper.js"></script>

<script src="https://cdn.jsdelivr.net/npm/elkjs/lib/elk.bundled.js"></script>
<script src="assets/js/cytoscape-elk.min.js"></script> -->


<div class="container">
    <br>
    <h1>Criminal Actions</h1>

    <p>Every mobster needs a specialty, perform yours and level up your skill, unlock permanent effects to your criminal
        activities and boost your reputation.</p>

    <br>

    <?php if (empty($unlockedSkills)): ?>
        <p>You need to select a specialization, select carefully as you won't have many changes to change your
            specialization in the future.</p>

        <div class="alert alert-warning">
            <strong>Note:</strong> You can only select one specialization, so choose wisely.
        </div>

        <br>

        <!-- Specialization 1: Stealth -->
        <div class="skill-option">
            <h2>Stealth</h2>
            <p>Master the art of stealth, allowing you to perform actions without being detected.</p>
            <button class="claim-button" onclick="claimSpecialization(1)">Claim Stealth
                Skill</button>
            <button onclick="openSkillTree(1)">Open Skill Tree</button>
        </div>

        <br>

        <!-- Specialization 2: Operations -->
        <div class="skill-option">
            <h2>Operations</h2>
            <p>Become an expert in operations, enhancing your efficiency and effectiveness in criminal activities.</p>
            <button class="claim-button" onclick="claimSpecialization(2)">Claim Operations
                Skill</button>
            <button onclick="openSkillTree(2)">Open Skill Tree</button>
        </div>

        <br>

        <!-- Specialization 3: Brawler -->
        <div class="skill-option">
            <h2>Brawler</h2>
            <p>Focus on brute strength and combat skills, making you a formidable opponent in any fight.</p>
            <button class="claim-button" onclick="claimSpecialization(3)">Claim Brawler
                Skill</button>
            <button onclick="openSkillTree(3)">Open Skill Tree</button>
        </div>

        <script>
            function claimSpecialization(id) {
                if (confirm("Are you sure you want to claim this specialization? This action cannot be undone.")) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'skilled_tasks.php';
                    form.innerHTML = `<input type="hidden" name="claim_specialization" value="${id}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function openSkillTree(id) {
                const url = 'skilltree.php?skilltree=' + encodeURIComponent(id || '1');
                const windowName = 'SkillTreeWindow';

                // Use precise feature string
                const features = [
                    'width=800',
                    'height=800',
                    'left=' + (screen.width - 1000) / 2,
                    'top=' + (screen.height - 1000) / 2,
                    'toolbar=no',
                    'menubar=no',
                    'scrollbars=no',
                    'resizable=no',
                    'status=no',
                    'location=no'
                ].join(',');

                window.open(url, windowName, features);
            }
        </script>

    <?php else: ?>

        <div class="task-list" id="task-list">
            <h2>It's a lovely day in <?php echo $user_class->cityname ?>, what will you do?</h2>
            <!-- JS will populate this -->
        </div>

        <div class="modal" id="task-modal">
            <div class="modal-content">
                <button class="close-btn" onclick="closeModal()">×</button>
                <h3 id="task-title">Title</h3>
                <p id="task-desc">Description here</p>
                <p><strong>Energy:</strong> <span id="task-energy"></span></p>
                <p><strong>Duration:</strong> <span id="task-duration"></span></p>
                <p><strong>Required Skills:</strong> <span id="task-skills"></span></p>
                <button class="start-button" onclick="startTask()">Start Task</button>
            </div>
        </div>

        <br> <br>

        <script>
            const tasks = [
                {
                    id: 1,
                    title: "Pocket Thief Round",
                    description: "Steal from busy crowds in the square.",
                    energy: 30,
                    duration: "2h",
                    skills: []
                },
                {
                    id: 2,
                    title: "Spy on Rival Player",
                    description: "Use your surveillance skills to gather intel.",
                    energy: 40,
                    duration: "3h",
                    skills: ["Surveillance"]
                },
                {
                    id: 3,
                    title: "Install Spyware in Gang",
                    description: "Sneak into a rival gang's HQ to plant spyware.",
                    energy: 100,
                    duration: "12h",
                    skills: ["Spyware Expert", "Preparation"]
                }
            ];

            function renderTasks() {
                const list = document.getElementById("task-list");
                tasks.forEach(task => {
                    const div = document.createElement("div");
                    div.className = "task-card";
                    div.innerHTML = `
        <h3>${task.title}</h3>
        <p>Energy: ${task.energy} • Time: ${task.duration}</p>
      `;
                    div.onclick = () => openTaskModal(task);
                    list.appendChild(div);
                });
            }

            function openTaskModal(task) {
                document.getElementById("task-title").textContent = task.title;
                document.getElementById("task-desc").textContent = task.description;
                document.getElementById("task-energy").textContent = task.energy;
                document.getElementById("task-duration").textContent = task.duration;
                document.getElementById("task-skills").textContent = task.skills.length ? task.skills.join(", ") : "None";
                document.getElementById("task-modal").style.display = "flex";
            }

            function closeModal() {
                document.getElementById("task-modal").style.display = "none";
            }

            function startTask() {
                alert("Task started!");
                closeModal();
            }

            renderTasks();
        </script>

        <div id="skilltree-wrapper" style="height:700px; position:relative;">
            <iframe src="/skilltree.php" style="width:100%; height:100%; border:0;" loading="lazy"
                referrerpolicy="no-referrer"></iframe>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>