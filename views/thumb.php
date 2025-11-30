<?php
require_once __DIR__ . '/../ImageHelper.php';

/** @var array $data */
$users = $data['users']; 

$cacheDir = getCacheDir();
?>

<link rel="stylesheet" href="assets/css/style.css">

<?php if (!empty($data['warnings'])): ?>
    <div class="warning-box">
        <?php foreach ($data['warnings'] as $w): ?>
            <p><?= htmlspecialchars($w) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1 class="page-title">USERS</h1>

<h3>Thumb view</h3>

<div style="display:flex; flex-wrap:wrap; gap:20px;">
    <?php foreach ($users as $u): ?>

        <?php
            $originalPath = $u->picture;
            $fileName = basename($originalPath);
            $cachePath = $cacheDir . '/' . $fileName;

            generateThumbnail($originalPath, $cachePath);
        ?>

        <div style="border:1px solid #ccc; padding:10px; width:150px; text-align:center;">
            <div>
                <img 
                    src="data/cache/<?= htmlspecialchars($fileName) ?>" 
                    alt="Picture of <?= htmlspecialchars(ucfirst($u->name) . ' ' . ucfirst($u->surname)) ?>"
                />
            </div> 
            <div>
                <?= htmlspecialchars(ucfirst($u->name)) ?>
            </div>  
            <div>
                <?= htmlspecialchars(ucfirst($u->surname)) ?>
            </div>
            <div>
                <?php
                $dt = new DateTime($u->last_login);
                echo $dt->format('d/m/Y H:i:s');
                ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="back-btn-container">
    <a class="back-btn" href="/">BACK</a>
</div>