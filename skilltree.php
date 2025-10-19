<?php

require_once 'includes/cache.php';
include_once 'includes/functions.php';
include_once "classes.php";

start_session_guarded();

if (!isset($_SESSION['id'])) {
    die();
}

$uid = $_SESSION['id'];
$user_class = new User($uid);

$title = "";
$skilltreeId = filter_input(INPUT_GET, 'skilltree', FILTER_VALIDATE_INT);
$interactive = false;
if (!$skilltreeId) {
    if (empty($user_class->skills)) {
        die("No skill tree available for this user. Supply a skill tree ID in the URL.");
    }

    $skilltreeInfo = get_skilltree_from_skill($user_class->skills[0]);
    $skilltree = get_skilltree_nodes($skilltreeInfo['id']);
    $title = $skilltreeInfo['title'];
    $interactive = true;
} else {
    $skilltree = get_skilltree_nodes($skilltreeId);
    $skilltreeInfo = get_skilltree($skilltreeId);
    if ($skilltreeInfo) {
        $title = $skilltreeInfo['title'];
    }
}

$playerSkillPoints = empty($user_class->skill_points) ? 0 : $user_class->skill_points;
$unlockedSkills = empty($user_class->skill_ids) ? [] : array_map('strval', explode(',', $user_class->skill_ids));

?>

<style>
    <?php if (!$interactive): ?>
        body {
            background: radial-gradient(circle at center, rgb(20, 31, 20), #0d0d0d);
            color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Prevent scrolling */
            height: 100%;
            width: 100%;
        }

    <?php else: ?>
        .skilltree {
            background: radial-gradient(circle at center, rgb(20, 31, 20), #0d0d0d);
            color: #f0f0f0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            border-radius: 12px;
        }

    <?php endif; ?>

    .skilltree {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .tippy-box[data-theme~='dark'] {
        background-color: #000;
        color: #f5f5f5;
        border: 1px solid #333;
        font-family: 'Roboto Mono', monospace;
        border-radius: 6px;
        padding: 8px;
    }

    .tooltip-title {
        font-weight: bold;
        font-size: 16px;
        margin: 0 0 4px 0;
    }

    .tooltip-desc {
        font-size: 13px;
        line-height: 1.3;
        margin: 0;
    }

    .skill-info {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(100%);
        background: #1a1a1a;
        color: #f0f0f0;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        min-width: 400px;
        z-index: 1000;
        transition: transform 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        pointer-events: none;
    }

    .skill-info.show {
        transform: translateX(-50%) translateY(0%);
        opacity: 1;
        pointer-events: auto;
    }

    .info-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .skill-icon {
        width: 64px;
        height: 64px;
        object-fit: contain;
        background-image: url('css/images/skilltree/node_bg.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 50%;
        border: 2px solid #444;
    }

    .text-content {
        flex-grow: 1;
    }

    .error-message {
        color: #ff4d4f;
        font-weight: bold;
        margin-top: 8px;
        margin-block-end: 0px;
        font-size: 12px;
    }

    .claim-button {
        background: #28a745;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        margin-top: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.2s ease;
    }

    .claim-button:hover {
        background: #218838;
    }

    .skill-points {
        position: absolute;
        bottom: 20px;
        left: 30px;
        background: #1a1a1a;
        color: #f0f0f0;
        padding: 12 16;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        min-width: 100px;
        max-width: 200px;
        z-index: 1000;
        pointer-events: none;
    }

    #skill-title {
        margin-block-start: 0px;
        margin-block-end: 0px;
    }

    #skill-description {
        margin-block-start: 8px;
        margin-block-end: 0px;
        font-size: 14px;
    }
</style>

<div>
    <?php if (!empty($title)): ?>
        <p style="font-size:36px;color:white;margin-top:6px;position:absolute;z-index:60;padding-left:16px;">
            <?php echo htmlspecialchars($title); ?>
        </p>
    <?php endif; ?>

    <div id="skill-tree" class="skilltree">
        <?php if ($interactive): ?>
            <div class="skill-points">
                <strong>Skill Points:</strong> <span id="skill-points"><?php echo $playerSkillPoints; ?></span>
            </div>
        <?php endif; ?>
        <div id="skill-info" class="skill-info">
            <div class="info-content">
                <img id="skill-icon" class="skill-icon" src="" alt="Skill Icon" />
                <div class="text-content">
                    <h3 id="skill-title">Skill Title</h3>
                    <p id="skill-description">Skill description goes here</p>
                    <p id="skill-error" class="error-message"></p>
                    <button id="claim-skill" class="claim-button">Claim Skill</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://unpkg.com/@popperjs/core@2"></script>
<script>
    if (typeof window.Popper === 'undefined' && typeof window.PopperCore !== 'undefined') {
        window.Popper = window.PopperCore;
    }

    window.tippy = window.tippy || window['tippy'];
</script>

<!-- Tippy + Popper -->
<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/shift-away.css" />
<link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/dark.css">
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.min.js"></script>

<!-- Cytoscape and Extensions -->
<script src="https://unpkg.com/cytoscape@3.28.0/dist/cytoscape.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cytoscape-popper@1.0.7/cytoscape-popper.js"></script>

<script src="https://cdn.jsdelivr.net/npm/elkjs/lib/elk.bundled.js"></script>
<script src="assets/js/cytoscape-elk.min.js"></script>

<script>
    (function initSkilltreeOnce() {
        // Prevent double-initialization if this fragment gets injected more than once
        if (window.__skilltreeInitialized) return;
        window.__skilltreeInitialized = true;

        // Register Cytoscape plugins once per page
        if (!window.__cytoscapePluginsSet) {
            if (typeof cytoscapeElk !== 'undefined') cytoscape.use(cytoscapeElk);
            if (typeof cytoscapePopper !== 'undefined') cytoscape.use(cytoscapePopper);
            window.__cytoscapePluginsSet = true;
        }

        let panBounds = {
            minX: -500,
            maxX: 1000,
            minY: -500,
            maxY: 1000,
        };

        const playerSkillPoints = <?php echo $playerSkillPoints; ?>;
        const unlockedSkillIds = <?php echo json_encode($unlockedSkills); ?>;
        const skillTreeValues = <?php echo json_encode(array_values($skilltree)); ?>;
        const interactive = <?php echo json_encode($interactive); ?>;

        const elements = [];
        skillTreeValues.forEach((skill) => {
            let classes = "";
            if (unlockedSkillIds.includes(String(skill.id))) {
                classes += "unlocked ";
            }
            if (!skill.parent) {
                classes += "core";
            }

            let edgeClasses = "";
            if (skill.parent) {
                if (unlockedSkillIds.includes(String(skill.parent))) {
                    edgeClasses += "edge-available ";
                } else {
                    edgeClasses += "edge-locked ";
                }
            }

            elements.push({
                data: {
                    id: String(skill.id),
                    label: skill.title,
                    description: skill.description,
                    icon: skill.icon,
                },
                classes: classes,
                style: {
                    "background-image": [
                        "url(css/images/skilltree/node_bg.png)",
                        `url(css/images/skilltree/${skill.icon})`,
                    ],
                    "background-fit": ["cover", "contain"],
                    "background-clip": ["node", "node"],
                    "background-width-relative-to": "inner",
                    "background-height-relative-to": "inner",
                },
            });

            if (skill.parent) {
                elements.push({
                    data: { source: String(skill.parent), target: String(skill.id) },
                    classes: edgeClasses,
                });
            }
        });

        const skillTree = cytoscape({
            container: document.getElementById("skill-tree"),
            elements: elements,
            style: [
                {
                    selector: "core",
                    style: { "active-bg-opacity": 0 }
                },
                {
                    selector: "node:active",
                    style: { "overlay-opacity": 0 }
                },
                {
                    selector: "node:selected",
                    style: { "transition-property": "border-width border-color" }
                },
                {
                    selector: "node.selected-pulse-a",
                    style: {
                        "transition-property": "border-width border-color",
                        "border-width": 2,
                        "border-color": "#888"
                    }
                },
                {
                    selector: "node.selected-pulse-b",
                    style: {
                        "transition-property": "border-width border-color",
                        "border-width": 4,
                        "border-color": "#aaa"
                    }
                },
                {
                    selector: "node.unlocked",
                    style: {
                        "border-width": 3,
                        "border-color": "#FFDC00",
                        "border-opacity": 1
                    }
                },
                {
                    selector: "node",
                    style: {
                        "background-image": "css/images/2025/node-bg.png",
                        "shape": "ellipse",
                        "width": 60,
                        "height": 60,
                        "background-color": "transparent",
                        "background-fit": "cover",
                        "color": "#fff",
                        "text-valign": "center",
                        "text-halign": "center",
                        "font-size": "28px"
                    }
                },
                {
                    selector: "node.core",
                    style: {
                        "width": 80,
                        "height": 80,
                        "background-color": "#FF851B",
                        "border-color": "#FFDC00",
                        "border-width": 4,
                        "font-size": "32px",
                        "text-outline-width": 2,
                        "text-outline-color": "#000"
                    }
                },
                {
                    selector: "edge.edge-available",
                    style: {
                        "line-color": "#aaa",
                        "width": 2
                    }
                },
                {
                    selector: "edge.edge-locked",
                    style: {
                        "line-color": "#555",
                        "width": 2
                    }
                },
                {
                    selector: "edge:selected",
                    style: { "overlay-opacity": 0 }
                }
            ],
            layout: {
                name: "elk",
                elk: { algorithm: "radial" }
            },
            minZoom: 0.5,
            maxZoom: 2,
            zoomingEnabled: true,
            userZoomingEnabled: true
        });

        // Expose for post-load adjustments (optional)
        window.__skillTree = skillTree;

        skillTree.ready(function () {
            skillTree.fit(null, 24);
            skillTree.nodes().ungrabify();

            skillTree.nodes().forEach(function (node) {
                const ref = node.popperRef();
                node.tippy = tippy(document.body, {
                    content: createTooltipContent(node),
                    allowHTML: true,
                    theme: "dark",
                    getReferenceClientRect: node.popperRef().getBoundingClientRect,
                    appendTo: document.body,
                    animation: "shift-away",
                    duration: [250, 150],
                    arrow: false,
                    interactive: true,
                    trigger: "manual",
                });
                node.on("mouseover", () => node.tippy.show());
                node.on("mouseout", () => node.tippy.hide());
            });
        });

        skillTree.on("pan", function () {
            const pan = skillTree.pan();
            const clampedPan = {
                x: Math.min(panBounds.maxX, Math.max(panBounds.minX, pan.x)),
                y: Math.min(panBounds.maxY, Math.max(panBounds.minY, pan.y)),
            };
            if (pan.x !== clampedPan.x || pan.y !== clampedPan.y) {
                skillTree.pan(clampedPan);
            }
        });

        skillTree.on("zoom", function () {
            const zoom = skillTree.zoom();
            panBounds = {
                minX: Math.min(-750, -750 * zoom),
                maxX: Math.max(1000, 1000 * zoom),
                minY: Math.min(-750, -750 * zoom),
                maxY: Math.max(1000, 1000 * zoom),
            };
        });

        // DOM refs (scoped inside the IIFE)
        const infoBox = document.getElementById("skill-info");
        const skillTitle = document.getElementById("skill-title");
        const skillDesc = document.getElementById("skill-description");
        const skillIcon = document.getElementById("skill-icon");
        const skillError = document.getElementById("skill-error");
        const claimButton = document.getElementById("claim-skill");

        skillTree.nodes().on("select", (e) => {
            const node = e.target;
            const data = node.data();

            // Check if parent is unlocked
            const incomingEdges = node.incomers("edge");
            const parents = incomingEdges.map((edge) => edge.source().id());
            if (parents.length === 0) {
                infoBox.classList.remove("show");
                return;
            }

            const parentUnlocked = parents.length === 0 || parents.every((id) => unlockedSkillIds.includes(id));

            skillTitle.textContent = data.label;
            skillDesc.textContent = data.description || "";
            skillIcon.src = `css/images/skilltree/${data.icon || "default.png"}`;
            skillError.textContent = "";
            claimButton.style.display = "none";

            if (interactive) {
                if (!parentUnlocked) {
                    skillError.textContent = "Parent skill needs to be unlocked before you can unlock this skill";
                    skillError.style.color = "#ff4d4f";
                } else if (unlockedSkillIds.includes(data.id)) {
                    skillError.textContent = "Skill unlocked";
                    skillError.style.color = "green";
                } else if (playerSkillPoints <= 0) {
                    skillError.textContent = "Not enough skill points to unlock";
                    skillError.style.color = "#ff4d4f";
                } else {
                    claimButton.style.display = "inline-block";
                    skillError.style.color = "#ff4d4f";
                }
            }

            startPulse(node);

            claimButton.onclick = () => {
                fetch(`ajax_skilltree.php?action=claim_skill&skill=${data.id}`, {
                    method: "GET",
                    headers: { "Content-Type": "application/json" },
                })
                    .then((response) => response.json())
                    .then((result) => {
                        if (result.error) {
                            skillError.textContent = result.error;
                        } else {
                            skillError.textContent = "Skill unlocked";
                            claimButton.style.display = "none";
                            unlockedSkillIds.push(data.id);

                            const sp = document.getElementById("skill-points");
                            if (sp) sp.textContent = result.remaining_points;

                            node.unselect();
                            stopPulse(node);
                            setTimeout(() => node.select(), 50);

                            const outgoingEdges = node.outgoers("edge");
                            outgoingEdges.forEach((edge) => {
                                edge.removeClass("edge-locked");
                                edge.addClass("edge-available");
                            });

                            node.addClass("unlocked");
                            node.style("border-color", "#FFDC00");
                            node.style("border-width", 3);
                        }
                    });
            };

            infoBox.classList.add("show");
        });

        skillTree.nodes().on("unselect", (e) => {
            stopPulse(e.target);
        });

        skillTree.on("tap", (e) => {
            if (e.target === skillTree) {
                infoBox.classList.remove("show");
            }
        });

        function createTooltipContent(node) {
            const title = node.data("label");
            const desc = node.data("description") || "Something went wrong!";
            return `
      <div class="tooltip-container">
        <h4 class="tooltip-title">${title}</h4>
        <p class="tooltip-desc">${desc}</p>
      </div>
    `;
        }

        const originalStyles = new Map();

        function startPulse(node) {
            const id = node.id();
            if (!originalStyles.has(id)) {
                originalStyles.set(id, {
                    borderWidth: node.style("border-width"),
                    borderColor: node.style("border-color"),
                });
            }

            function pulseOut() {
                node.animate(
                    { style: { "border-width": "3px", "border-color": "#aaa" } },
                    { duration: 1000, complete: pulseIn }
                );
            }

            function pulseIn() {
                node.animate(
                    { style: { "border-width": "2px", "border-color": "#888" } },
                    { duration: 1000, complete: pulseOut }
                );
            }

            pulseOut();
        }

        function stopPulse(node) {
            node.stop();
            const original = originalStyles.get(node.id());
            if (original) {
                node.animate({
                    style: {
                        "border-width": original.borderWidth,
                        "border-color": original.borderColor,
                    },
                });
                originalStyles.delete(node.id());
            }
        }
    })();
</script>