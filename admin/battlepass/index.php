<?php
require_once __DIR__ . '/../../header.php';
require_once __DIR__ . '/../../includes/repositories/battlepass_repository.php';

if ($user_class->admin < 1) {
    exit();
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$repo = new BattlepassRepository();
$categories = $repo->getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Battlepass Editor</title>
    <style>
        .bp-admin {
            max-width: 1100px;
        }

        .bp-card {
            background: var(--cc-surface, #111);
            color: var(--cc-text, #eee);
            border: 1px solid var(--cc-border, #2a2a2a);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .25);
        }

        .bp-head {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bp-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .bp-badge {
            font-size: .75rem;
            padding: 2px 8px;
            border: 1px solid #3a3a3a;
            border-radius: 999px;
            color: #bbb;
        }

        .bp-body {
            padding: 16px;
        }

        .bp-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .bp-field {
            display: grid;
            gap: 6px;
        }

        .bp-field label {
            font-size: .9rem;
            color: var(--cc-muted, #aaa);
        }

        .bp-input,
        .bp-select {
            background: #181818;
            color: #eee;
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            padding: 10px;
            width: 100%;
        }

        .bp-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .bp-help {
            font-size: .85rem;
            color: var(--cc-muted, #9aa);
        }

        .bp-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .bp-btn {
            border: 1px solid #2e2e2e;
            background: #202020;
            color: #eee;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
        }

        .bp-btn:hover {
            background: #262626;
        }

        .bp-btn.primary {
            background: #2d5fff;
            border-color: #2d5fff;
        }

        .bp-btn.primary:hover {
            filter: brightness(1.05);
        }

        .bp-btn.ghost {
            background: transparent;
        }

        .bp-section {
            margin-top: 18px;
        }

        .bp-table-wrap {
            overflow: auto;
            border: 1px solid #2a2a2a;
            border-radius: 10px;
        }

        table.bp-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        table.bp-table th,
        table.bp-table td {
            padding: 10px;
            border-bottom: 1px solid #242424;
            text-align: left;
        }

        table.bp-table thead th {
            position: sticky;
            top: 0;
            background: #141414;
            z-index: 1;
            font-weight: 600;
            font-size: .9rem;
        }

        table.bp-table input {
            width: 100%;
            box-sizing: border-box;
        }

        .bp-del {
            color: #ff6b6b;
            border-color: #ff6b6b;
            background: transparent;
        }

        .bp-del:hover {
            background: rgba(255, 107, 107, .1);
        }

        .bp-spacer {
            height: 8px;
        }

        .bp-sticky-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-top: 1px solid rgba(255, 255, 255, .06);
            background: #121212;
            border-radius: 0 0 12px 12px;
        }

        .bp-note {
            font-size: .85rem;
            color: var(--cc-muted, #9aa);
        }

        .bp-switch {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .bp-checkbox {
            transform: scale(1.05);
        }

        .bp-tag {
            font-size: .8rem;
            letter-spacing: .02em;
            color: #aaa;
        }

        .bp-mini {
            font-size: .85rem;
            color: #9aa;
        }

        @media (max-width: 900px) {
            .bp-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="bp-admin">
        <!-- Main save form -> save_full -->
        <form action="/admin/battlepass/bp_handler.php?action=save_full" method="post" id="bpForm">
            <input type="hidden" name="csrf"
                value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES); ?>">
            <input type="hidden" id="bpCategoryId" name="category_id" value="">
            <input type="hidden" id="bpMonthYear" name="month_year" value="">

            <div class="bp-card">
                <div class="bp-head">
                    <div class="bp-title">Battlepass Editor</div>
                </div>

                <div class="bp-body">
                    <div class="bp-grid">
                        <div class="bp-field">
                            <label for="bpMonth">Battlepass month</label>
                            <input class="bp-input" type="month" id="bpMonth" required>
                            <div class="bp-help">Saved to <code>bp_category.month_year</code> as <code>mm-YYYY</code>.
                            </div>
                        </div>

                        <div class="bp-field">
                            <label for="bpExisting">Load/Edit existing</label>
                            <select id="bpExisting" class="bp-select">
                                <option value="">— Select to edit —</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int) $cat['id']; ?>">
                                        #<?php echo (int) $cat['id']; ?> —
                                        <?php echo htmlspecialchars($cat['month_year'] ?? '', ENT_QUOTES); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="bp-section">
                        <div class="bp-row" style="justify-content: space-between;">
                            <div class="bp-field" style="max-width: 560px;">
                                <label>Quick notes</label>
                                <div class="bp-mini">
                                    • Challenges → <code>bp_category_challenges</code> (type, amount, prize,
                                    is_premium)<br>
                                    • Prizes → <code>bp_category_prizes</code> (cost, type, amount, entity_id,
                                    is_premium)
                                </div>
                            </div>
                            <div class="bp-actions">
                                <button class="bp-btn ghost" type="button" id="btnAddChallenge">+ Add Challenge</button>
                                <button class="bp-btn ghost" type="button" id="btnAddPrize">+ Add Prize</button>
                            </div>
                        </div>
                    </div>

                    <!-- Challenges -->
                    <div class="bp-section">
                        <div class="bp-row" style="justify-content: space-between; align-items: baseline;">
                            <h3 style="margin:0;">Challenges</h3>
                            <span class="bp-tag">bp_category_challenges</span>
                        </div>
                        <div class="bp-spacer"></div>
                        <div class="bp-table-wrap">
                            <table class="bp-table" id="tblChallenges">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Prize</th>
                                        <th>Premium?</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Prizes -->
                    <div class="bp-section">
                        <div class="bp-row" style="justify-content: space-between; align-items: baseline;">
                            <h3 style="margin:0;">Prizes</h3>
                            <span class="bp-tag">bp_category_prizes</span>
                        </div>
                        <div class="bp-spacer"></div>
                        <div class="bp-table-wrap">
                            <table class="bp-table" id="tblPrizes">
                                <thead>
                                    <tr>
                                        <th>Cost</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Entity ID</th>
                                        <th>Premium?</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="bp-sticky-footer">
                    <div class="bp-note">Submitting replaces all rows atomically via <code>action=save_full</code>.
                    </div>
                    <div class="bp-actions">
                        <button type="submit" class="bp-btn primary">Save</button>
                        <button type="button" class="bp-btn" id="btnReset">Reset</button>
                    </div>
                </div>
            </div>

            <datalist id="dlTypes">
                <option value="win_fights">
                <option value="missions_completed">
                <option value="collect_souls">
                <option value="raid_wins">
                <option value="boss_damage">
                <option value="items_used">
            </datalist>
        </form>

        <!-- Update month_year for selected category -->
        <form action="/admin/battlepass/bp_handler.php?action=update_category" method="post" id="bpUpdateMonthForm"
            style="margin-top:16px;">
            <input type="hidden" name="csrf"
                value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES); ?>">
            <input type="hidden" name="id" id="updCatId" value="">
            <label class="bp-mini">Update month label for selected category:</label>
            <div class="bp-row">
                <input class="bp-input" type="text" name="month_year" id="updMonthYear" placeholder="mm-YYYY"
                    style="max-width:180px;">
                <button class="bp-btn" type="submit">Update Label</button>
            </div>
        </form>

        <!-- Delete selected category -->
        <form action="/admin/battlepass/bp_handler.php?action=delete_category" method="post" id="bpDeleteForm"
            style="margin-top:10px;">
            <input type="hidden" name="csrf"
                value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES); ?>">
            <input type="hidden" name="id" id="delCatId" value="">
            <button class="bp-btn bp-del" type="submit"
                onclick="return confirm('Delete this category and all its rows?');">Delete Selected Category</button>
        </form>
    </div>

    <!-- Row templates -->
    <template id="tmplChallengeRow">
        <tr>
            <td><input class="bp-input" list="dlTypes" type="text" name="challenges[__IDX__][type]"
                    placeholder="e.g., win_fights" required></td>
            <td><input class="bp-input" type="number" min="0" step="1" name="challenges[__IDX__][amount]"
                    placeholder="25" required></td>
            <td><input class="bp-input" type="number" min="0" step="1" name="challenges[__IDX__][prize]"
                    placeholder="1000" required></td>
            <td><label class="bp-switch"><input class="bp-checkbox" type="checkbox"
                        name="challenges[__IDX__][is_premium]" value="1"><span>Premium</span></label></td>
            <td><button type="button" class="bp-btn bp-del btnDelRow">Delete</button></td>
        </tr>
    </template>

    <template id="tmplPrizeRow">
        <tr>
            <td><input class="bp-input" type="number" min="0" step="1" name="prizes[__IDX__][cost]" placeholder="100"
                    required></td>
            <td><input class="bp-input" list="dlTypes" type="text" name="prizes[__IDX__][type]"
                    placeholder="item | cash | souls" required></td>
            <td><input class="bp-input" type="number" min="0" step="1" name="prizes[__IDX__][amount]" placeholder="1"
                    required></td>
            <td><input class="bp-input" type="number" min="0" step="1" name="prizes[__IDX__][entity_id]"
                    placeholder="0 (optional)"></td>
            <td><label class="bp-switch"><input class="bp-checkbox" type="checkbox" name="prizes[__IDX__][is_premium]"
                        value="1"><span>Premium</span></label></td>
            <td><button type="button" class="bp-btn bp-del btnDelRow">Delete</button></td>
        </tr>
    </template>

    <script>
        (function () {
            const $ = (s, r = document) => r.querySelector(s);
            const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

            // Elements
            const bpMonth = $('#bpMonth');
            const bpMonthYear = $('#bpMonthYear');
            const bpCategoryId = $('#bpCategoryId');
            const updCatId = $('#updCatId');
            const delCatId = $('#delCatId');
            const updMonthYear = $('#updMonthYear');

            const tblChallenges = $('#tblChallenges tbody');
            const tblPrizes = $('#tblPrizes tbody');
            const tmplCh = $('#tmplChallengeRow');
            const tmplPr = $('#tmplPrizeRow');

            let chIdx = 0, prIdx = 0;

            // Helpers
            const monthToLabel = (yyyyMm) => {
                if (!yyyyMm) return '';
                const [y, m] = yyyyMm.split('-');
                return `${m}-${y}`;
            };
            const labelToMonth = (mmYYYY) => {
                if (!mmYYYY) return '';
                const [m, y] = mmYYYY.split('-');
                return `${y}-${m}`;
            };

            function syncMonthYearHidden() {
                bpMonthYear.value = monthToLabel(bpMonth.value);
            }

            function addChallengeRow(pref = {}) {
                const node = tmplCh.content.firstElementChild.cloneNode(true);
                $$('input', node).forEach(inp => inp.name = inp.name.replace('__IDX__', chIdx));
                if (pref.type) node.querySelector(`[name="challenges[${chIdx}][type]"]`).value = pref.type;
                if (Number.isFinite(pref.amount)) node.querySelector(`[name="challenges[${chIdx}][amount]"]`).value = pref.amount;
                if (Number.isFinite(pref.prize)) node.querySelector(`[name="challenges[${chIdx}][prize]"]`).value = pref.prize;
                if (pref.is_premium) node.querySelector(`[name="challenges[${chIdx}][is_premium]"]`).checked = !!pref.is_premium;
                tblChallenges.appendChild(node); chIdx++;
            }

            function addPrizeRow(pref = {}) {
                const node = tmplPr.content.firstElementChild.cloneNode(true);
                $$('input', node).forEach(inp => inp.name = inp.name.replace('__IDX__', prIdx));
                if (Number.isFinite(pref.cost)) node.querySelector(`[name="prizes[${prIdx}][cost]"]`).value = pref.cost;
                if (pref.type) node.querySelector(`[name="prizes[${prIdx}][type]"]`).value = pref.type;
                if (Number.isFinite(pref.amount)) node.querySelector(`[name="prizes[${prIdx}][amount]"]`).value = pref.amount;
                if (Number.isFinite(pref.entity_id)) node.querySelector(`[name="prizes[${prIdx}][entity_id]"]`).value = pref.entity_id;
                if (pref.is_premium) node.querySelector(`[name="prizes[${prIdx}][is_premium]"]`).checked = !!pref.is_premium;
                tblPrizes.appendChild(node); prIdx++;
            }

            // Delete row (delegation)
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btnDelRow');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
            });

            // Add row buttons
            $('#btnAddChallenge').addEventListener('click', () => addChallengeRow());
            $('#btnAddPrize').addEventListener('click', () => addPrizeRow());

            // Reset form
            $('#btnReset').addEventListener('click', () => {
                $('#bpForm').reset();
                bpCategoryId.value = '';
                bpMonthYear.value = '';
                updCatId.value = delCatId.value = '';
                updMonthYear.value = '';
                tblChallenges.innerHTML = '';
                tblPrizes.innerHTML = '';
                chIdx = prIdx = 0;
                addChallengeRow();
                addPrizeRow();
            });

            // Month sync
            bpMonth.addEventListener('change', () => {
                syncMonthYearHidden();
                // If user picked a month manually, clear selected category to avoid accidental overwrite
                bpCategoryId.value = '';
                updCatId.value = delCatId.value = '';
                updMonthYear.value = bpMonthYear.value;
            });

            // Load existing category via handler
            $('#bpExisting').addEventListener('change', async (e) => {
                const id = e.target.value;
                if (!id) return;
                try {
                    const res = await fetch(`/admin/battlepass/bp_handler.php?action=get_category&id=${encodeURIComponent(id)}`);
                    const json = await res.json();
                    if (!json.ok) throw new Error(json.error || 'Failed to load');

                    // Set ids for save/update/delete
                    bpCategoryId.value = json.category.id;
                    updCatId.value = delCatId.value = json.category.id;

                    // Month sync (mm-YYYY -> YYYY-MM)
                    if (json.category.month_year) {
                        bpMonth.value = labelToMonth(json.category.month_year);
                        syncMonthYearHidden();
                        updMonthYear.value = json.category.month_year;
                    }

                    // Hydrate rows
                    tblChallenges.innerHTML = ''; tblPrizes.innerHTML = '';
                    chIdx = prIdx = 0;
                    (json.challenges || []).forEach(addChallengeRow);
                    (json.prizes || []).forEach(addPrizeRow);

                    if (!json.challenges?.length) addChallengeRow();
                    if (!json.prizes?.length) addPrizeRow();

                } catch (err) {
                    alert('Error: ' + err.message);
                }
            });

            // Initial UX: one empty row each
            addChallengeRow();
            addPrizeRow();

            if (bpMonth.value) syncMonthYearHidden();
        })();
    </script>

</body>

</html>