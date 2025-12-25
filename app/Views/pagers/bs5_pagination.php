<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end mb-0">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" aria-label="First" class="page-link">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" aria-label="Previous" class="page-link">
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a href="#" class="page-link">&laquo;</a>
            </li>
            <li class="page-item disabled">
                <a href="#" class="page-link">&lt;</a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" aria-label="Next" class="page-link">
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" aria-label="Last" class="page-link">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a href="#" class="page-link">&gt;</a>
            </li>
            <li class="page-item disabled">
                <a href="#" class="page-link">&raquo;</a>
            </li>
        <?php endif ?>
    </ul>
</nav>