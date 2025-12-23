<?php 
$queryParams = $pagination['params'] ?? $_GET ?? [];
$totalPages = $pagination['total_pages'] ?? 0;
$currentPage = $pagination['current_page'] ?? 1;
$hasPrev = $pagination['has_prev'] ?? false;
$hasNext = $pagination['has_next'] ?? false;

if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($hasPrev): ?>
                <li class="page-item">
                    <?php $queryParams['page'] = $currentPage - 1; ?>
                    <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>">Previous</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <?php $queryParams['page'] = $i; ?>
                    <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($hasNext): ?>
                <li class="page-item">
                    <?php $queryParams['page'] = $currentPage + 1; ?>
                    <a class="page-link" href="?<?php echo http_build_query($queryParams); ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>