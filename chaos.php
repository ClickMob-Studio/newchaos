<?php

include 'header.php';
include_once 'includes/repositories/chaos_repository.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$chaosRepository = new ChaosRepository($db);

$state = $chaosRepository->getChaosUserState($user_class->id);
$lanterns = $chaosRepository->getLanterns();
$currentLantern = array_values(array_filter(
    $lanterns,
    fn($l) => (int) $l['id'] === (int) ($state['lantern_equipped'] ?? 0)
))[0] ?? null;

$currentRank = (int) ($currentLantern['rank'] ?? 0);
$nextLantern = null;
foreach ($lanterns as $l) {
    if ($currentLantern === null) {
        $nextLantern = $l;
        break;
    }
    if ((int) $l['rank'] > $currentRank) {
        $nextLantern = $l;
        break;
    }
}

$perHour = (int) ($currentLantern['souls_hour'] ?? 0);
$lastHour = (int) $chaosRepository->getSoulsThisHour($user_class->id);

$progressPct = $perHour > 0 ? min(100, (int) round(($lastHour / $perHour) * 100)) : 0;

$now = time();
$hourStart = strtotime(date('Y-m-d H:00:00'));
$hourEnd = $hourStart + 3600;
$secondsLeft = max(0, $hourEnd - $now);

$haveSouls = (int) ($state['souls_current'] ?? 0);
$price = (int) ($nextLantern['soul_price'] ?? 0);
$canBuy = $nextLantern && $price > 0 && $haveSouls >= $price;
$needPct = $price > 0 ? min(100, (int) round(($haveSouls / $price) * 100)) : 0;

$passRows = $chaosRepository->getChaosPass();             // SELECT * ORDER BY curse_level ASC, id ASC
$passUser = $chaosRepository->getChaosPassUser($user_class->id); // ['is_premium'=>0/1]
$claimedIds = $chaosRepository->getChaosPassClaims($user_class->id);

$isPremium = (int) ($passUser['is_premium'] ?? 0) === 1;
$userExp = (int) ($state['curse_exp'] ?? 0);

// Find the “current segment” (closest next threshold)
$nextReq = null;
$prevReq = 0;
foreach ($passRows as $row) {
    $req = (int) $row['curse_exp_req'];
    if ($req > $userExp) {
        $nextReq = $req;
        break;
    }
    $prevReq = $req;
}
$segmentDen = $nextReq === null ? 1 : max(1, $nextReq - $prevReq);
$segmentNum = $nextReq === null ? 1 : max(0, min($userExp - $prevReq, $segmentDen));
$overallPct = (int) min(100, round(($segmentNum / $segmentDen) * 100));

function rr_label(array $r): string
{
    $qty = (int) $r['reward_qty'];
    $type = strtolower((string) $r['reward_type']);
    if ($type === 'item') {
        $it = Get_Item($r['reward_ref_id']);
        return $it['itemname'] . " x{$r['reward_qty']}";
    }
    if ($type === 'exp')
        return $qty . '% EXP';
    if ($type === 'money')
        return number_format($qty) . ' Money';
    if ($type === 'points')
        return number_format($qty) . ' Points';
    return 'Reward';
}

function rr_image(array $r): string
{
    $type = strtolower((string) $r['reward_type']);
    if ($type === 'item') {
        $it = Get_Item($r['reward_ref_id']);
        return $it['image'];
    }
    if ($type === 'exp')
        return 'css/images/exp.png';
    if ($type === 'money')
        return 'css/images/money.png';
    if ($type === 'points')
        return 'css/images/points.png';

    return '';
}

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function back()
{
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        $_SESSION['flash'] = ['err', 'Session expired. Please try again.'];
        back();
    }

    $action = $_POST['action'] ?? '';
    $userId = $user_class->id;

    try {
        switch ($action) {
            case 'claim_all': {
                $result = $chaosRepository->claimAllAvailableRewards($userId);

                if ($result['rewards']) {
                    $_SESSION['flash'] = ['ok', 'Claimed ' . count($result['rewards']) . ' rewards', $result['rewards']];
                } else {
                    $locked = count($res['premium_locked'] ?? []);
                    $msg = 'Nothing to claim right now.';
                    if ($locked)
                        $msg .= ' (' . $locked . ' premium locked)';
                    $_SESSION['flash'] = ['info', $msg];
                }
                break;
            }

            case 'claim_one': {
                $passId = (int) ($_POST['pass_id'] ?? 0);

                $state = $chaosRepository->getChaosUserState($userId);
                $passUser = $chaosRepository->getChaosPassUser($userId);
                $isPremium = (int) ($passUser['is_premium'] ?? 0) === 1;

                $rows = $chaosRepository->getChaosPass();
                $row = null;
                foreach ($rows as $r) {
                    if ((int) $r['id'] === $passId) {
                        $row = $r;
                        break;
                    }
                }
                if (!$row) {
                    $_SESSION['flash'] = ['err', 'Tier not found'];
                    back();
                }

                $tier = (int) ($row['curse_level'] ?? 0);
                $currentLv = (int) ($state['curse_level'] ?? 0);

                $reached = ($currentLv >= $tier);
                $premiumOk = ((int) $row['is_premium'] === 0) || $isPremium;

                $claimed = in_array($passId, $chaosRepository->getChaosPassClaims($userId), true);

                if (!$reached) {
                    $_SESSION['flash'] = ['info', 'Your curse power is too low to claim this reward'];
                } elseif (!$premiumOk) {
                    $_SESSION['flash'] = ['info', 'Premium is required to unlock this reward'];
                } elseif ($claimed) {
                    $_SESSION['flash'] = ['ok', 'You have already claimed this reward'];
                } else {
                    $db->query("INSERT IGNORE INTO chaos_pass_claims (user_id, pass_id) VALUES (?, ?)");
                    $db->execute([$userId, $passId]);

                    if ($db->affected_rows() > 0) {
                        $rewarded = $chaosRepository->grantChaosReward($userId, $row);
                        $chaosRepository->bustChaosPassClaimsCache($userId);

                        $label = rr_label($row);
                        $_SESSION['flash'] = ['ok', 'Reward claimed: ' . $rewarded];
                    } else {
                        $_SESSION['flash'] = ['info', 'Already claimed'];
                    }
                }
                break;
            }

            case 'upgrade_premium': {
                if ($user_class->credits < 500) {
                    $_SESSION['flash'] = ['err', 'You do not have enough credits to upgrade to the premium pass.'];
                    back();
                    break;
                }

                perform_query("UPDATE grpgusers SET credits = credits - 500 WHERE id = ?", [$userId]);

                $chaosRepository->upgradePassToPremium($userId);
                $_SESSION['flash'] = ['ok', 'Premium activated!'];
                break;
            }

            case 'buy_lantern': {
                $lanternId = (int) ($_POST['lantern_id'] ?? 0);

                if ($lanternId <= 0) {
                    $_SESSION['flash'] = ['err', 'Invalid lantern selection.'];
                    break;
                }

                try {
                    $out = $chaosRepository->upgradeLanternWithSouls($userId, $lanternId);

                    if (is_array($out) && !empty($out['ok'])) {
                        $_SESSION['flash'] = ['ok', $out['message'] ?? 'Lantern upgraded!'];
                    } else {
                        $msg = is_string($out) ? $out : 'Could not upgrade lantern.';
                        $_SESSION['flash'] = ['info', $msg];
                    }
                } catch (Throwable $e) {
                    $_SESSION['flash'] = ['err', 'Upgrade failed. Please try again.'];
                }

                break;
            }

            default:
                $_SESSION['flash'] = ['info', 'No action'];
        }
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['err', 'Something went wrong. Please try again.'];
    }

    back();
}

?>

<style>
    :root {
        --bg: #0c0f14;
        --surface: #121722;
        --surface2: #1a2133;
        --muted: #9aa3b2;
        --text: #e7ebf3;
        --accent: #7aa2ff;
        --accent2: #3dd9b6;
        --gold: #f6c25a;
        --ok: #58d68d;
        --danger: #ff6b6b;
        --radius: 14px;
        --shadow: 0 10px 28px rgba(0, 0, 0, .4);
    }

    .bp3 {
        margin-top: 18px;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 16px;
        padding: 14px;
        box-shadow: var(--shadow);
        color: var(--text);
    }

    .bp3-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .bp3-head h3 {
        margin: 0;
        font-weight: 800;
    }

    .bp3-progress {
        position: relative;
        height: 10px;
        background: rgba(255, 255, 255, .08);
        border-radius: 999px;
        overflow: hidden;
    }

    .bp3-progress>i {
        position: absolute;
        inset: 0;
        width: 0%;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
    }

    .bp3-sub {
        display: flex;
        gap: 6px;
        align-items: center;
        justify-content: flex-end;
        font-size: .9rem;
        color: var(--muted);
        margin-top: 6px;
    }

    .bp3-track {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .bp3-tile {
        background: #221212;
        border: 1px solid rgba(255, 255, 255, .10);
        border-radius: 14px;
        padding: 10px;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .bp3-thumb {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: #331a1a;
        aspect-ratio: 1 / 1;
        display: grid;
        place-items: center;
    }

    .bp3-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        user-select: none;
        -webkit-user-drag: none;
        pointer-events: none;
    }

    .bp3-badges {
        position: absolute;
        top: 8px;
        left: 8px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .pill {
        padding: 4px 8px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .14);
        font-size: .75rem;
        font-weight: 700;
        background: rgba(255, 255, 255, .03);
    }

    .pill.premium {
        background: rgba(255, 209, 90, .12);
        border-color: rgba(255, 209, 90, .28);
        color: #ffd15a
    }

    .pill.free {
        background: rgba(61, 217, 182, .12);
        border-color: rgba(61, 217, 182, .28);
        color: #8cf0d9
    }

    .bp3-level {
        position: absolute;
        bottom: 8px;
        left: 8px;
        font-weight: 800;
        font-size: .8rem;
        background: #0b101a;
        color: #cad3e6;
        padding: .2rem .45rem;
        border-radius: 8px;
    }

    .bp3-title {
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.2
    }

    .bp3-req {
        font-size: .85rem;
        color: var(--muted)
    }

    .bp3-req strong {
        color: var(--text);
        font-weight: 800
    }

    .bp3-mini {
        position: relative;
        height: 7px;
        background: rgba(255, 255, 255, .08);
        border-radius: 999px;
        overflow: hidden;
    }

    .bp3-mini>i {
        position: absolute;
        inset: 0;
        width: 0%;
        background: linear-gradient(90deg, #9b0707, #ffe400, #008b07);
    }

    .bp3-actions {
        display: flex;
        gap: 8px;
        margin-top: 2px
    }

    .bp3-claim {
        background: #6f1212;
        color: #fff;
        border: 0;
        padding: .55rem .8rem;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        font-size: .85rem;
    }

    .bp3-claim[disabled] {
        background: #432c2c;
        color: #97a0b3;
        cursor: not-allowed
    }

    .bp3-state {
        margin-left: auto;
        font-size: .78rem;
        font-weight: 800;
        padding: .35rem .55rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, .12);
    }

    .bp3-state.claimed {
        background: linear-gradient(90deg, var(--ok), #86e6bd);
        color: #062015;
        border-color: transparent
    }

    .bp3-state.locked {
        background: #1f2636;
        color: #8fa0bf
    }

    .bp3-state.ready {
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        color: #07141a;
        border-color: transparent
    }

    /* subtle motion */
    .bp3-progress>i,
    .bp3-mini>i {
        transition: width .6s ease
    }

    /* LANTERN */
    .chaos-grid {
        display: grid;
        grid-template-areas:
            "current stats"
            "current curse";
        grid-template-columns: 1.1fr 1fr;
        gap: 18px;
        align-items: start;
    }

    @media (max-width: 960px) {
        .chaos-grid {
            grid-template-areas:
                "current"
                "stats"
                "curse";
            grid-template-columns: 1fr;
        }
    }

    .card {
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 16px;
        padding: 14px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .28);
    }

    .card.current {
        grid-area: current;
        text-align: center;
    }

    .card.stats {
        grid-area: stats;
    }

    .card.stats+.card.stats {
        grid-area: curse;
    }

    .lantern-image {
        width: 260px;
        height: 260px;
        margin: 0 auto 10px;
        display: grid;
        place-items: center;
        background: radial-gradient(ellipse at center, rgba(255, 255, 255, .08), rgba(0, 0, 0, 0));
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .lantern-image img {
        width: 200px;
        height: 200px;
        object-fit: contain;
        user-select: none;
        -webkit-user-drag: none;
        pointer-events: none;
        filter: drop-shadow(0 10px 16px rgba(0, 0, 0, .35));
    }

    .lantern-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin-top: 6px
    }

    .lantern-meta {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin-top: 8px;
        flex-wrap: wrap
    }

    .pill {
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .14);
        font-size: .85rem;
        font-weight: 700;
        background: rgba(255, 255, 255, .03)
    }

    .pill.bonus {
        background: rgba(255, 209, 90, .12);
        border-color: rgba(255, 209, 90, .28)
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 8px 0
    }

    .stat-label {
        color: var(--cc-muted, #9aa3ad);
        font-size: .9rem
    }

    .stat-value {
        font-size: 1.2rem;
        font-weight: 700
    }

    .muted {
        color: var(--cc-muted, #9aa3ad);
        margin: 0 4px
    }

    .progress {
        position: relative;
        height: 10px;
        background: rgba(255, 255, 255, .08);
        border-radius: 99px;
        overflow: hidden;
        margin: 8px 0 4px;
    }

    .progress-fill {
        position: absolute;
        inset: 0;
        width: 0%;
        background: linear-gradient(90deg, #ff9a00, #ffd15a);
        width: 0%
    }

    .reset-row {
        display: flex;
        gap: 8px;
        align-items: center;
        font-size: .95rem;
        margin-top: 2px
    }

    .divider {
        height: 1px;
        background: rgba(255, 255, 255, .08);
        margin: 12px 0
    }

    /* Next Lantern */
    .next-lantern {
        grid-column: 1 / -1;
    }

    /* full width row under the grid */
    @media (min-width: 961px) {
        .next-lantern {
            grid-column: 1 / -1;
        }
    }

    .nl-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px
    }

    .nl-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800
    }

    .nl-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap
    }

    .nl-body {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 16px;
        align-items: center
    }

    @media (max-width: 720px) {
        .nl-body {
            grid-template-columns: 1fr
        }
    }

    .nl-image {
        width: 220px;
        height: 220px;
        display: grid;
        place-items: center;
        background: radial-gradient(ellipse at center, rgba(255, 255, 255, .08), rgba(0, 0, 0, 0));
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .nl-image img {
        width: 180px;
        height: 180px;
        object-fit: contain;
        user-select: none;
        -webkit-user-drag: none;
        pointer-events: none;
        filter: drop-shadow(0 8px 14px rgba(0, 0, 0, .35));
    }

    .nl-info {
        display: flex;
        flex-direction: column;
        gap: 8px
    }

    .nl-title {
        font-size: 1.05rem;
        font-weight: 800
    }

    .nl-row {
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .nl-label {
        color: var(--cc-muted, #9aa3ad);
        font-size: .9rem
    }

    .nl-value {
        font-weight: 700;
        font-size: 1.05rem
    }

    .nl-sub {
        display: flex;
        gap: 6px;
        align-items: center;
        font-size: .95rem;
        margin-top: 2px
    }

    .nl-actions {
        margin-top: 10px
    }

    .btn-upgrade {
        font-size: .95rem;
        font-weight: 800;
        border: none;
        border-radius: 10px;
        padding: 10px 14px;
        background: linear-gradient(90deg, #ff7a18, #ffb347);
        color: #1b0d00;
        cursor: pointer;
        box-shadow: 0 6px 16px rgba(255, 150, 0, .25);
    }

    .btn-upgrade[disabled] {
        opacity: .6;
        cursor: not-allowed;
        box-shadow: none
    }

    .bp3-progress-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }

    .bp3-claimall-form {
        align-self: flex-start;
    }

    .bp3-claimall {
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        color: #fff;
        border: none;
        font-weight: 700;
        padding: 0.7rem 1.2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .3);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .bp3-claimall:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, .4);
    }

    .bp3-claimall:disabled {
        background: #2c3343;
        color: #97a0b3;
        cursor: not-allowed;
    }

    /* Flash */
    .flash {
        margin: 0px 0 16px;
        border-radius: 12px;
        padding: 10px 14px;
        border: 1px solid rgba(255, 255, 255, .12);
        background: rgba(255, 255, 255, .04);
        box-shadow: var(--shadow);
    }

    .flash-ok {
        border-color: rgba(88, 214, 141, .35);
        background: rgba(88, 214, 141, .08);
    }

    .flash-err {
        border-color: rgba(255, 107, 107, .35);
        background: rgba(255, 107, 107, .08);
    }

    .flash-info {
        border-color: rgba(122, 162, 255, .35);
        background: rgba(122, 162, 255, .08);
    }

    .flash-title {
        font-weight: 800;
    }

    .flash-list {
        margin: 0;
        padding-left: 18px;
        line-height: 1.35;
    }

    /* Modal */
    .cc-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .cc-modal.show {
        display: flex;
    }

    .cc-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        backdrop-filter: blur(4px);
    }

    .cc-modal-card {
        position: relative;
        z-index: 1;
        width: min(520px, 92vw);
        background: rgb(16 7 7 / 67%);
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 16px;
        padding: 16px;
        box-shadow: var(--shadow);
        color: var(--text);
    }

    .cc-modal-card h3 {
        margin: 0 0 8px;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .cc-modal-card p {
        margin: 0 0 12px;
        color: var(--muted);
    }

    .cc-modal-card p strong {
        color: #fbff68;
    }

    .cc-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .cc-btn {
        border: none;
        border-radius: 12px;
        padding: .6rem 1rem;
        font-weight: 800;
        cursor: pointer;
    }

    .cc-btn-primary {
        background: linear-gradient(90deg, #ffb347, #ffd15a);
        color: #201300;
    }

    .cc-btn-ghost {
        background: transparent;
        color: #cbd3e6;
        border: 1px solid rgba(255, 255, 255, .18);
    }

    .cc-btn-primary:hover {
        filter: brightness(1.05);
    }

    .cc-btn-ghost:hover {
        background: rgba(255, 255, 255, .06);
    }
</style>

<!-- Message -->
<?php if (!empty($_SESSION['flash'])):
    [$type, $title, $lines] = [
        $_SESSION['flash'][0] ?? 'ok',
        $_SESSION['flash'][1] ?? '',
        $_SESSION['flash'][2] ?? []
    ];
    unset($_SESSION['flash']); // clear it so it shows only once
    $cls = match ($type) {
        'ok' => 'flash-ok',
        'err' => 'flash-err',
        'info' => 'flash-info',
        default => 'flash-ok'
    };
    ?>
    <div class="flash <?= h($cls) ?>">
        <?php if ($title): ?>
            <div class="flash-title"><?= h($title) ?></div>
        <?php endif; ?>
        <?php if ($lines && is_array($lines)): ?>
            <ul class="flash-list">
                <?php foreach ($lines as $ln): ?>
                    <li><?= h($ln) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>


<!-- Lantern -->
<div class="chaos-grid">
    <!-- Current Lantern -->
    <section class="card current">
        <div class="lantern-image">
            <img src="<?= h($currentLantern['image'] ?? '/assets/halloween/placeholder-lantern.png') ?>"
                alt="<?= h($currentLantern['name'] ?? 'No lantern equipped') ?>" draggable="false" />
        </div>
        <div class="lantern-title">
            <?= h($currentLantern['name'] ?? 'No lantern equipped') ?>
        </div>

        <?php if ($currentLantern): ?>
            <div class="lantern-meta">
                <span class="pill"><?= number_format((int) $currentLantern['souls_hour']) ?> Souls / hour</span>
                <?php if (!empty($currentLantern['soul_bonus'])): ?>
                    <span class="pill bonus">+<?= (int) $currentLantern['soul_bonus'] ?>% Bonus</span>
                <?php else: ?>
                    <span class="pill muted">No bonus</span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="lantern-meta">
                <span class="pill muted">No lantern equipped</span>
            </div>
        <?php endif; ?>
    </section>

    <!-- Souls & Hourly -->
    <section class="card stats">
        <div class="stat-row">
            <div class="stat-label">Souls this hour</div>
            <div class="stat-value">
                <strong><?= number_format($lastHour) ?></strong>
                <span class="muted">/</span>
                <strong><?= number_format($perHour) ?></strong>
            </div>
        </div>
        <div class="progress">
            <div class="progress-fill" style="width: <?= $progressPct ?>%"></div>
        </div>
        <div class="reset-row">
            <span class="muted">Resets in</span>
            <span id="reset-countdown" data-seconds="<?= (int) $secondsLeft ?>">—:—</span>
            <span class="muted">at <?= date('H:00') ?></span>
        </div>

        <div class="divider"></div>

        <div class="stat-row">
            <div class="stat-label">Souls (current)</div>
            <div class="stat-value"><?= number_format((int) floor($state['souls_current'] ?? 0)) ?></div>
        </div>
        <div class="stat-row">
            <div class="stat-label">Souls (total collected)</div>
            <div class="stat-value"><?= number_format((int) floor($state['souls_collected'] ?? 0)) ?></div>
        </div>
    </section>

    <!-- Curse -->
    <section class="card stats">
        <div class="stat-row">
            <div class="stat-label">Curse EXP</div>
            <div class="stat-value">
                <?= number_format((int) ($state['curse_exp'] ?? 0)) ?>
            </div>
        </div>
        <div class="stat-row">
            <div class="stat-label">Curse level</div>
            <div class="stat-value">
                <?= number_format((int) ($state['curse_level'] ?? 0)) ?>
            </div>
        </div>
    </section>
</div>

<!-- Next Lantern -->
<?php if ($nextLantern): ?>
    <br />
    <section class="card next-lantern">
        <div class="nl-header">
            <h3>Next Lantern</h3>
            <div class="nl-badges">
                <?php if (!empty($nextLantern['soul_bonus'])): ?>
                    <span class="pill bonus">+<?= (int) $nextLantern['soul_bonus'] ?>% Souls</span>
                <?php endif; ?>
                <span class="pill"><?= number_format((int) $nextLantern['souls_hour']) ?> / hour</span>
            </div>
        </div>

        <div class="nl-body">
            <div class="nl-image">
                <img src="<?= h($nextLantern['image'] ?? '/assets/halloween/placeholder-lantern.png') ?>"
                    alt="<?= h($nextLantern['name'] ?? 'Next lantern') ?>" draggable="false" />
            </div>

            <div class="nl-info">
                <div class="nl-title"><?= h($nextLantern['name'] ?? 'Next lantern') ?></div>

                <div class="nl-row">
                    <div class="nl-label">Cost</div>
                    <div class="nl-value"><?= number_format($price) ?> Souls</div>
                </div>

                <div class="progress">
                    <div class="progress-fill" style="width: <?= $needPct ?>%"></div>
                </div>
                <div class="nl-sub">
                    <span><?= number_format($haveSouls) ?></span>
                    <span class="muted">/</span>
                    <span><?= number_format($price) ?></span>
                    <span class="muted">Souls</span>
                </div>

                <form method="post" action="" class="nl-actions">
                    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                    <input type="hidden" name="action" value="buy_lantern">
                    <input type="hidden" name="lantern_id" value="<?= (int) $nextLantern['id'] ?>">
                    <button class="btn-upgrade" <?= $canBuy ? '' : 'disabled' ?>>Buy & Equip</button>
                </form>
            </div>
        </div>
    </section>
<?php endif; ?>

<br />

<section class="bp3" aria-label="Battle Pass">
    <?php if (!$isPremium): ?>
        <section class="bp3 premium-upgrade" style="margin-top:14px;">
            <div class="premium-header">
                <div class="premium-info">
                    <h3>Premium Track</h3>
                    <p class="premium-sub">
                        Unlock even more rewards with the PREMIUM halloween pass!
                    </p>
                </div>

                <?php $premiumPrice = 500; ?>
                <form id="upgradePremiumForm" method="post" action="">
                    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf'] ?? '') ?>">
                    <input type="hidden" name="action" value="upgrade_premium">
                    <button id="upgrade-open" class="btn-upgrade" type="button">Upgrade to Premium</button>
                </form>

                <!-- Modal -->
                <div id="upgrade-modal" class="cc-modal" aria-hidden="true" role="dialog" aria-labelledby="upg-title"
                    aria-modal="true">
                    <div class="cc-modal-backdrop"></div>
                    <div class="cc-modal-card">
                        <h3 id="upg-title">Confirm Purchase</h3>
                        <p>This will cost <strong><?= number_format($premiumPrice) ?> gold</strong> .<br />Are you sure you
                            want to purchase the premium pass?</p>
                        <div class="cc-modal-actions">
                            <button id="upgrade-cancel" type="button" class="cc-btn cc-btn-ghost">Cancel</button>
                            <button id="upgrade-confirm" type="button" class="cc-btn cc-btn-primary">Yes, purchase</button>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    <?php else: ?>
        <section class="bp3 premium-active" style="margin-top:14px;">
            <div class="premium-header">
                <div class="premium-info">
                    <h3>Premium Track</h3>
                    <p class="premium-sub">Your Premium benefits are active — enjoy all premium rewards!</p>
                </div>
                <div class="premium-badge">
                    <span class="pill premium">PREMIUM ACTIVE</span>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <br />


    <div class="bp3-head">
        <h3>Battle Pass</h3>
    </div>

    <div class="bp3-progress-wrapper">
        <form method="post" action="" class="bp3-claimall-form">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="claim_all">
            <button class="bp3-claimall" type="submit">Claim All Available Rewards</button>
        </form>

        <div class="bp3-progress">
            <i style="width: <?= $overallPct ?>%"></i>
        </div>
        <div class="bp3-sub">
            <?php if ($nextReq === null): ?>
                <span>Max tier reached</span>
            <?php else: ?>
                <span><?= number_format($userExp - $prevReq) ?></span>
                <span>/</span>
                <span><?= number_format($nextReq - $prevReq) ?> XP</span>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $currentLevel = (int) ($state['curse_level'] ?? 0);
    $currentExp = (int) ($state['curse_exp'] ?? 0);

    $reqByLevel = [];
    foreach ($passRows as $r) {
        $reqByLevel[(int) $r['curse_level']] = (int) $r['curse_exp_req'];
    }

    $reqAt = function (int $lvl) use ($reqByLevel) {
        return $reqByLevel[$lvl] ?? 0;
    };
    ?>
    <div class="bp3-track">
        <?php foreach ($passRows as $row):
            $tier = (int) ($row['curse_level'] ?? 0);
            $req = (int) $row['curse_exp_req'];
            $claimed = in_array((int) $row['id'], $claimedIds, true);
            $prem = (int) $row['is_premium'] === 1;

            $reached = ($currentLevel >= $tier);
            $claimable = $reached && !$claimed && (!$prem || $isPremium);

            $remaining = 0;
            if (!$reached) {
                $remaining = max(0, $req - ($reqAt($currentLevel) + $currentExp));
            }

            if ($reached) {
                $tilePct = 100;
            } elseif ($tier === $currentLevel + 1) {
                $segTotal = max(1, $req - $reqAt($currentLevel));
                $tilePct = (int) min(99, round(($currentExp / $segTotal) * 100));
            } else {
                $tilePct = 0;
            }

            $rewardLabel = rr_label($row);
            $img = rr_image($row) ?: '/assets/halloween/pass-placeholder.png';
            ?>
            <div class="bp3-tile">
                <div class="bp3-thumb">
                    <img src="<?= h($img) ?>" alt="<?= h($row['name'] ?? $rewardLabel) ?>" style="width:100px;height:100px;"
                        draggable="false">
                    <div class="bp3-badges">
                        <?php if ($prem): ?>
                            <span class="pill premium">PREMIUM</span>
                        <?php else: ?>
                            <span class="pill free">FREE</span>
                        <?php endif; ?>
                    </div>
                    <div class="bp3-level">Lv <?= $tier ?: '&mdash;' ?></div>
                </div>

                <div class="bp3-title"><?= h($row['name'] ?? $rewardLabel) ?></div>

                <div class="bp3-req">
                    <?php if (!$reached): ?>
                        Requires <strong><?= number_format($remaining) ?></strong> XP
                    <?php else: ?>
                        <strong>Unlocked</strong>
                    <?php endif; ?>
                </div>

                <div class="bp3-mini"><i style="width: <?= $tilePct ?>%"></i></div>

                <div class="bp3-actions">
                    <?php if ($claimed): ?>
                        <button class="bp3-claim" disabled>Claimed</button>
                    <?php elseif ($prem && !$isPremium): ?>
                        <button class="bp3-claim" disabled>Premium Required</button>
                    <?php elseif (!$reached): ?>
                        <button class="bp3-claim" disabled>Locked</button>
                    <?php else: ?>
                        <form method="post" action="">
                            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                            <input type="hidden" name="action" value="claim_one">
                            <input type="hidden" name="pass_id" value="<?= (int) $row['id'] ?>">
                            <button class="bp3-claim" <?= $claimable ? '' : 'disabled' ?>>Claim</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>


<script>
    (function () {
        requestAnimationFrame(function () {
            var fills = document.querySelectorAll('.progress-fill');
            fills.forEach(function (f) { f.style.transition = 'width .6s ease'; });
        });

        var el = document.getElementById('reset-countdown');
        if (!el) return;

        var seconds = parseInt(el.getAttribute('data-seconds') || '0', 10);
        function fmt(t) {
            var m = Math.floor(t / 60), s = t % 60;
            return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        }
        function tick() {
            if (seconds < 0) seconds = 0;
            el.textContent = fmt(seconds);
            if (seconds === 0) return;
            seconds -= 1;
            setTimeout(tick, 1000);
        }
        tick();
    })();

    (function () {
        var openBtn = document.getElementById('upgrade-open');
        var modal = document.getElementById('upgrade-modal');
        var cancel = document.getElementById('upgrade-cancel');
        var confirmBtn = document.getElementById('upgrade-confirm');
        var form = document.getElementById('upgradePremiumForm');

        if (!openBtn || !modal || !form) return;

        function open() { modal.classList.add('show'); modal.setAttribute('aria-hidden', 'false'); }
        function close() { modal.classList.remove('show'); modal.setAttribute('aria-hidden', 'true'); }

        openBtn.addEventListener('click', open);
        cancel && cancel.addEventListener('click', close);
        confirmBtn && confirmBtn.addEventListener('click', function () { form.submit(); });

        modal.addEventListener('click', function (e) { if (e.target === modal || e.target.classList.contains('cc-modal-backdrop')) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    })();
</script>


<?php
include 'footer.php';
?>