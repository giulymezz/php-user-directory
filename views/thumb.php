<?php

require_once __DIR__ . '/../ImageHelper.php';

/** @var array $data */
$users = $data['users']; 

$pageTitle = "Results - Thumb view";

$cacheDir = getCacheDir();

ob_start();

?>

<div class="container">

    <h1 class="page-title">Results</h1>

    <h3>Thumb view</h3>

    <?php if (empty($users)): ?>

        <p class="no-results">The search produced no results</p>

        <div class="button-container">
            <a href="/" class="button button-secondary">Back</a>
        </div>

    <?php else: ?>
        
        <div class="thumb-grid">
            <?php foreach ($users as $u): ?>

                <?php
                    $originalPath = $u->picture;
                    $fileName = basename($originalPath);
                    $cachePath = $cacheDir . '/' . $fileName;

                    generateThumbnail($originalPath, $cachePath);
                ?>

                <div class="thumb-item">
                    <img 
                        src="data/cache/<?= htmlspecialchars($fileName) ?>" 
                        alt="Picture of <?= htmlspecialchars(ucfirst($u->name) . ' ' . ucfirst($u->surname)) ?>"
                    />

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

        <div class="button-container">
            <a href="/" class="button button-secondary">Back</a>
        </div>

    <?php endif; ?>

</div>

<?php 
 
$content = ob_get_clean();

require __DIR__ . '/layout.php';