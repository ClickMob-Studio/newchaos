<?php
require_once __DIR__ . '/../../header.php';
require_once __DIR__ . '/../../includes/repositories/battlepass_repository.php';

if ($user_class->admin < 1) {
    exit();
}

// CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$repo = new BattlepassRepository();
$categories = $repo->getAllCategories();
$items = $repo->LoadItems();

$self = '/admin/battlepass/index.php';

$saved = isset($_GET['saved']);
$updated = isset($_GET['updated']);
$deleted = isset($_GET['deleted']);
$flashId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Battlepass Editor</title>
    <style>
        :root {
            --bp-row-h: 44px;
        }

        .bp-input,
        .bp-select {
            height: calc(var(--bp-row-h) - 2px);
            line-height: normal;
            display: block;
            margin: 0;
        }

        .bp-admin {
            max-width: 1100px;
            margin: 20px auto;
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
            font-size: 1.1rem;
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
            min-width: 780px;
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

        table.bp-table input,
        table.bp-table select {
            width: 100%;
            box-sizing: border-box;
        }

        table.bp-table tbody td {
            padding: 0 10px;
            height: var(--bp-row-h);
            vertical-align: middle;
            line-height: 1;
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
            font-size: 1rem;
            color: var(--cc-muted, #9aa);
        }

        .bp-mini {
            font-size: 1rem;
            color: #9aa;
        }

        table.bp-table td.center {
            display: flex;
            align-items: center;
            justify-content: center;
            height: var(--bp-row-h);
            padding: 10px;
        }

        td.center .cell-fill {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .td-center-inner {
            height: var(--bp-row-h);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bp-checkbox {
            margin: 0;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .col-mini {
            width: 64px;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .flash {
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid #335533;
            background: #112211;
            color: #bfe6bf;
            border-radius: 8px;
        }

        .flash.error {
            border-color: #553333;
            background: #221111;
            color: #f0b3b3;
        }

        @media (max-width: 900px) {
            .bp-grid {
                grid-template-columns: 1fr;
            }
        }

        button {
            margin: 0px !important;
        }
    </style>
</head>

<body>

    <div class="bp-admin">

        <?php if ($saved): ?>
            <div class="flash">Saved successfully<?php if ($flashId): ?> (category #<?php echo $flashId; ?>)<?php endif; ?>.
            </div>
        <?php elseif ($updated): ?>
            <div class="flash">Category label updated<?php if ($flashId): ?> (#<?php echo $flashId; ?>)<?php endif; ?>.
            </div>
        <?php elseif ($deleted): ?>
            <div class="flash">Category deleted.</div>
        <?php endif; ?>

        <!-- MAIN SAVE FORM -> save_full -->
        <form action="/admin/battlepass/bp_handler.php?action=save_full" method="post" id="bpForm">
            <input type="hidden" name="csrf"
                value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES); ?>">
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($self, ENT_QUOTES); ?>">
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
                            <div class="bp-help">Stored as <code>mm-YYYY</code> in <code>bp_category.month_year</code>.
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
                            <div class="bp-help">Loads via
                                <code>GET /admin/battlepass/bp_handler.php?action=get_category&id=…</code>
                            </div>
                        </div>
                    </div>

                    <div class="bp-section">
                        <div class="bp-row" style="justify-content: space-between;">
                            <div class="bp-field" style="max-width: 560px;">
                                <label>Quick notes</label>
                                <div class="bp-mini">
                                    • Challenges → <code>type, amount, prize, is_premium</code><br>
                                    • Prizes → <code>cost, type, amount, entity_id, is_premium</code>
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
                            <span class="bp-mini">Table: bp_category_challenges</span>
                        </div>
                        <div class="bp-spacer"></div>
                        <div class="bp-table-wrap">
                            <table class="bp-table" id="tblChallenges">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Prize</th>
                                        <th class="col-mini">★</th>
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
                            <span class="bp-mini">Table: bp_category_prizes</span>
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
                                        <th class="col-mini">★</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bp-section">
                        <div style="justify-content: space-between; align-items: center;">
                            <div class="bp-mini">
                                Duplicate the current editor contents to another month.
                            </div>
                            <div class="bp-row">
                                <input class="bp-input" type="month" id="dupMonth" style="max-width: 180px;">
                                <button type="button" class="bp-btn" id="btnDuplicateTo">Duplicate to month</button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="bp-sticky-footer">
                    <div class="bp-note">Submitting replaces all rows atomically.</div>
                    <div class="bp-actions">
                        <button type="submit" class="bp-btn primary">Save</button>
                        <button type="button" class="bp-btn" id="btnReset">Reset</button>
                    </div>
                </div>
            </div>
        </form>

        <form action="/admin/battlepass/bp_handler.php?action=update_category" method="post" id="bpUpdateMonthForm"
            style="margin-top:16px;">
            <input type="hidden" name="csrf"
                value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES); ?>">
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($self, ENT_QUOTES); ?>">
            <input type="hidden" name="id" id="updCatId" value="">
            <label class="bp-mini">Update month label for selected category:</label>
            <div class="bp-row">
                <input class="bp-input" type="text" name="month_year" id="updMonthYear" placeholder="mm-YYYY"
                    style="max-width:180px;">
                <button class="bp-btn" type="submit">Update Label</button>
            </div>
        </form>

        <form action="/admin/battlepass/bp_handler.php?action=delete_category" method="post" id="bpDeleteForm"
            style="margin-top:10px;">
            <input type="hidden" name="csrf"
                value="<?php echo htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES); ?>">
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($self, ENT_QUOTES); ?>">
            <input type="hidden" name="id" id="delCatId" value="">
            <button class="bp-btn bp-del" type="submit"
                onclick="return confirm('Delete this category and all its rows?');">Delete Selected Category</button>
        </form>
    </div>

    <datalist id="itemsList">
        <?php foreach ($items as $it): ?>
            <option value="<?php echo (int) $it['id']; ?>">
                <?php echo htmlspecialchars($it['itemname'] ?? ('#' . (int) $it['id']), ENT_QUOTES); ?>
            </option>
        <?php endforeach; ?>
    </datalist>

    <template id="tmplChallengeRow">
        <tr>
            <td>
                <select class="bp-select" name="challenges[__IDX__][type]" required>
                    <option value="crimes">crimes</option>
                    <option value="mugs">mugs</option>
                    <option value="busts">busts</option>
                    <option value="backalley">backalley</option>
                    <option value="trains">trains</option>
                    <option value="attacks">attacks</option>
                </select>
            </td>
            <td><input class="bp-input" type="number" min="0" step="1" name="challenges[__IDX__][amount]"
                    placeholder="25" required></td>
            <td><input class="bp-input" type="number" min="0" step="1" name="challenges[__IDX__][prize]"
                    placeholder="1000" required></td>
            <td class="center">
                <div class="cell-fill">
                    <input type="checkbox" class="bp-checkbox" name="challenges[__IDX__][is_premium]" value="1">
                </div>
            </td>
            <td><button type="button" class="bp-btn bp-del btnDelRow">Delete</button></td>
        </tr>
    </template>

    <template id="tmplPrizeRow">
        <tr>
            <td><input class="bp-input" type="number" min="0" step="1" name="prizes[__IDX__][cost]" placeholder="100"
                    required></td>
            <td>
                <select class="bp-select prize-type" name="prizes[__IDX__][type]" required>
                    <option value="money">money</option>
                    <option value="raid_tokens">raid_tokens</option>
                    <option value="points">points</option>
                    <option value="item">item</option>
                    <option value="exp">exp</option>
                </select>
            </td>
            <td><input class="bp-input" type="number" min="0" step="1" name="prizes[__IDX__][amount]" placeholder="1"
                    required></td>
            <td>
                <input class="bp-input entity-num" type="number" min="0" step="1" name="prizes[__IDX__][entity_id]"
                    placeholder="0">
                <input class="bp-input entity-item" list="itemsList" placeholder="Search item…" style="display:none;">
            </td>
            <td class="center">
                <div class="cell-fill">
                    <input type="checkbox" class="bp-checkbox" name="prizes[__IDX__][is_premium]" value="1">
                </div>
            </td>
            <td><button type="button" class="bp-btn bp-del btnDelRow">Delete</button></td>
        </tr>
    </template>

    <script>
        (function () {
            const $ = (s, r = document) => r.querySelector(s);
            const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

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

            const dupMonth = $('#dupMonth');
            const btnDuplicate = $('#btnDuplicateTo');

            let chIdx = 0, prIdx = 0;

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
                $$('input,select', node).forEach(inp => inp.name = inp.name.replace('__IDX__', chIdx));
                if (pref.type) node.querySelector(`[name="challenges[${chIdx}][type]"]`).value = pref.type;
                if (Number.isFinite(pref.amount)) node.querySelector(`[name="challenges[${chIdx}][amount]"]`).value = pref.amount;
                if (Number.isFinite(pref.prize)) node.querySelector(`[name="challenges[${chIdx}][prize]"]`).value = pref.prize;
                if (pref.is_premium) node.querySelector(`[name="challenges[${chIdx}][is_premium]"]`).checked = !!pref.is_premium;
                tblChallenges.appendChild(node); chIdx++;
            }

            function configurePrizeEntityUI(row, typeValue, idx, prefillEntityId) {
                const num = row.querySelector('.entity-num');
                const item = row.querySelector('.entity-item');

                if (typeValue === 'item') {
                    num.style.display = 'none'; num.disabled = true; num.name = '';
                    item.style.display = ''; item.disabled = false; item.name = `prizes[${idx}][entity_id]`;
                    if (prefillEntityId != null) item.value = String(prefillEntityId);
                } else {
                    item.style.display = 'none'; item.disabled = true; item.name = '';
                    num.style.display = ''; num.disabled = false; num.name = `prizes[${idx}][entity_id]`;
                    if (prefillEntityId != null) num.value = String(prefillEntityId);
                }
            }

            function addPrizeRow(pref = {}) {
                const node = tmplPr.content.firstElementChild.cloneNode(true);
                $$('input,select', node).forEach(inp => { if (inp.name) inp.name = inp.name.replace('__IDX__', prIdx); });
                const typeSel = node.querySelector('.prize-type');
                typeSel.addEventListener('change', () => configurePrizeEntityUI(node, typeSel.value, prIdx, null));

                if (Number.isFinite(pref.cost)) node.querySelector(`[name="prizes[${prIdx}][cost]"]`).value = pref.cost;
                if (pref.type) typeSel.value = pref.type;
                if (Number.isFinite(pref.amount)) node.querySelector(`[name="prizes[${prIdx}][amount]"]`).value = pref.amount;
                if (pref.is_premium) node.querySelector(`[name="prizes[${prIdx}][is_premium]"]`).checked = !!pref.is_premium;

                configurePrizeEntityUI(node, typeSel.value, prIdx, Number.isFinite(pref.entity_id) ? pref.entity_id : null);
                tblPrizes.appendChild(node); prIdx++;
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btnDelRow');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
            });

            $('#btnAddChallenge').addEventListener('click', () => addChallengeRow());
            $('#btnAddPrize').addEventListener('click', () => addPrizeRow());

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

            bpMonth.addEventListener('change', () => {
                syncMonthYearHidden();
                bpCategoryId.value = '';
                updCatId.value = delCatId.value = '';
                updMonthYear.value = bpMonthYear.value;
            });

            $('#bpExisting').addEventListener('change', async (e) => {
                const id = e.target.value;
                if (!id) return;
                try {
                    const res = await fetch(`/admin/battlepass/bp_handler.php?action=get_category&id=${encodeURIComponent(id)}`);
                    const json = await res.json();
                    if (!json.ok) throw new Error(json.error || 'Failed to load');

                    bpCategoryId.value = json.category.id;
                    updCatId.value = delCatId.value = json.category.id;

                    if (json.category.month_year) {
                        bpMonth.value = labelToMonth(json.category.month_year);
                        syncMonthYearHidden();
                        updMonthYear.value = json.category.month_year;
                    }

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

            $('#btnDuplicateTo').addEventListener('click', () => {
                if (!dupMonth.value) { alert('Pick a target month'); return; }
                bpMonth.value = dupMonth.value;
                syncMonthYearHidden();
                bpCategoryId.value = '';
                updCatId.value = delCatId.value = '';
                updMonthYear.value = bpMonthYear.value;
                $('#bpForm').submit();
            });

            addChallengeRow();
            addPrizeRow();
            if (bpMonth.value) syncMonthYearHidden();
        })();
    </script>

</body>

</html>