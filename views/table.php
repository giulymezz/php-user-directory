<?php
/** @var array $data */   
$users = $data['users'];
$sort = $data['sort'];
$dir = $data['dir'];

function sortLink(string $field, ?string $currentSort, string $currentDir, array $data): string {
    $newDir = 'asc';
    if ($currentSort === $field && $currentDir === 'asc') {
        $newDir = 'desc';
    }

    $params = [
        'view' => 'table',
        'sort' => $field,
        'dir'  => $newDir,
        'active'  => $data['active']  ?? '',
        'from'    => $data['from']    ?? '',
        'to'      => $data['to']      ?? '',
        'name'    => $data['name']    ?? '',
        'surname' => $data['surname'] ?? '',
    ];

    return '?' . http_build_query($params);
}
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="table-wrapper">
    <?php if (!empty($data['warnings'])): ?>
        <div class="warning-box">
            <?php foreach ($data['warnings'] as $w): ?>
                <p><?= htmlspecialchars($w) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h1 class="page-title">USERS</h1>

    <h3>Table view</h3>

    <table class="user-table">
        <thead>   
            <tr>
                <th>
                    <a href="<?= sortLink('id', $sort, $dir, $data) ?>">
                        ID <?= $sort === 'id' ? ($dir === 'asc' ? '↑' : '↓') : '' ?>   
                    </a>   
                </th>
                <th>
                    <a href="<?= sortLink('name', $sort, $dir, $data) ?>"> 
                        NAME <?= $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '' ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortLink('surname', $sort, $dir, $data) ?>">
                        SURNAME <?= $sort === 'surname' ? ($dir === 'asc' ? '↑' : '↓') : '' ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortLink('active', $sort, $dir, $data) ?>">
                        ACTIVE <?= $sort === 'active' ? ($dir === 'asc' ? '↑' : '↓') : '' ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortLink('last_login', $sort, $dir, $data) ?>">
                        LAST LOGIN <?= $sort === 'last_login' ? ($dir === 'asc' ? '↑' : '↓') : '' ?>
                    </a>
                </th>
                <th>PICTURE</th>
                <th>
                    <a href="<?= sortLink('rating', $sort, $dir, $data) ?>">
                        RATING <?= $sort === 'rating' ? ($dir === 'asc' ? '↑' : '↓') : '' ?>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($u->id) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars(ucfirst($u->name)) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars(ucfirst($u->surname)) ?>
                    </td>
                    <td>
                        <?= (int)$u->active ?>
                    </td>
                    <td>
                        <?php
                        $dt = new DateTime($u->last_login);
                        echo $dt->format('d/m/Y H:i:s');
                        ?>
                    </td>
                    <td>
                        <img 
                            src="<?= htmlspecialchars($u->picture) ?>" 
                            alt="Picture of <?= htmlspecialchars(ucfirst($u->name) . ' ' . ucfirst($u->surname)) ?>"
                        />
                    </td>
                    <td>
                        <?= htmlspecialchars($u->rating) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="back-btn-container">
        <a href="index.php" class="back-btn">Back</a>
    </div>

</div>