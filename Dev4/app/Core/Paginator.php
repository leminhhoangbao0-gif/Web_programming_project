<?php
namespace Atlas\Core;

final class Paginator
{
    private int $page;

    public function __construct(private int $total, int $page, private int $perPage)
    {
        $this->page = max(1, min($page, $this->pages()));
    }

    public function page(): int { return $this->page; }
    public function pages(): int { return max(1, (int)ceil($this->total / max(1, $this->perPage))); }
    public function offset(): int { return ($this->page - 1) * $this->perPage; }
    public function perPage(): int { return $this->perPage; }

    public function render(string $path, array $query = []): string
    {
        if ($this->pages() <= 1) return '';
        $html = '<nav class="pagination">';
        for ($i = 1; $i <= $this->pages(); $i++) {
            $cls = $i === $this->page ? ' class="active"' : '';
            $html .= '<a' . $cls . ' href="' . e(url($path, [...$query, 'page' => $i])) . '">' . $i . '</a>';
        }
        return $html . '</nav>';
    }
}
