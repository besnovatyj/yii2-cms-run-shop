<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\RunShop;

use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesAdminMenu;
use Besnovatyj\Contracts\module\ProvidesBootstrap;
use Besnovatyj\Contracts\module\ProvidesDirectories;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Contracts\module\ProvidesOptions;
use Besnovatyj\Contracts\menu\MenuTarget;
use Besnovatyj\Contracts\menu\MenuTargetProvider;
use Besnovatyj\Kernel\module\CmsModule;
use Besnovatyj\RunShop\entities\category\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

/**
 * Модуль магазина RunShop (полнофункциональный: Yookassa, Wishlist, Coupon, OrderMerchant).
 */
class Module extends CmsModule implements
    DeclaresModule,
    ProvidesAdminMenu,
    ProvidesBootstrap,
    ProvidesDirectories,
    ProvidesMigrations,
    ProvidesOptions,
    MenuTargetProvider
{
    public const bool EDITABLE = true;
    public const string VERSION = '1.0.0';
    public const string MODULE_ID = 'RunShop';

    public static function moduleId(): string { return self::MODULE_ID; }
    public static function moduleVersion(): string { return self::VERSION; }
    public static function isEditable(): bool { return self::EDITABLE; }
    public static function moduleConfig(): array { return require __DIR__ . '/config/config.php'; }
    public static function adminMenu(): array { return require __DIR__ . '/config/adminMenu.php'; }
    public static function options(): array { return require __DIR__ . '/config/options.php'; }
    public static function migrationPath(): string { return __DIR__ . '/migrations'; }
    public static function migrationNamespace(): ?string { return __NAMESPACE__ . '\\migrations'; }
    public static function directories(): array { return ['@static/origin/RunShop', '@static/cache/RunShop']; }
    public static function bootstrapClasses(): array { return [Bootstrap::class]; }

    /**
     * Цели для построения пунктов меню. Реализация {@see MenuTargetProvider};
     * вызывается только модулем меню, если он установлен.
     *
     * @return MenuTarget[]
     */
    public function menuTargets(): array
    {
        return [
            new MenuTarget('/RunShop/catalog/category', 'Категория каталога', 'slug'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string,string>
     */
    public function menuCandidates(string $route): array
    {
        return match (ltrim($route, '/')) {
            'RunShop/catalog/category' => $this->categorySlugMap(),
            default => [],
        };
    }

    /**
     * Карта `slug => подпись` (с отступом по глубине дерева) для категорий каталога.
     *
     * @return array<string,string>
     */
    private function categorySlugMap(): array
    {
        return (new TreeQueryScope(Category::class))->dropdownTree(keyAttribute: 'slug', indent: '— ');
    }
}
