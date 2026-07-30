<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Mcp;

use Mcp\Capability\Registry\PromptReference;
use Mcp\Capability\Registry\ResourceReference;
use Mcp\Capability\Registry\ResourceTemplateReference;
use Mcp\Capability\Registry\ToolReference;
use Mcp\Capability\RegistryInterface;
use Mcp\Exception\InvalidCursorException;
use Mcp\Schema\Page;
use Mcp\Schema\Prompt;
use Mcp\Schema\ResourceDefinition;
use Mcp\Schema\ResourceTemplate;
use Mcp\Schema\Tool;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * `tools/list` を、 現在の OAuth2 トークンが呼べる Tool だけに絞る {@see RegistryInterface} デコレータ。
 * 呼べない Tool を一覧に出さない (最小権限)。
 *
 * mcp-bundle の `mcp.registry` を装飾し、 `ListToolsHandler` が読む {@see getTools()} だけを上書きする。
 * 各 Tool の必要 scope ({@see McpToolScopeMap}) を {@see AuthorizationCheckerInterface} で検査する。
 *
 * これは可視性の制御であって、 呼び出しの拒否ではない。 実際の拒否は {@see ScopeEnforcingReferenceHandler}
 * が call 時に fail-closed で担保する (二重防御。 本層が外れても未認可 Tool は実行されない)。
 *
 * トークンが無い経路 (CLI 等) は絞らず素通しする。 つまり list 層は fail-open で、 `/admin/mcp` の
 * OAuth2 firewall がトークンを保証することに依存する。 firewall 外の経路から `getTools()` に到達すると、
 * Tool 名と入力スキーマは露出する (データ本体は call 層が守る)。
 */
final readonly class ScopeFilteringRegistry implements RegistryInterface
{
    public function __construct(
        private RegistryInterface $inner,
        private AuthorizationCheckerInterface $authorizationChecker,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    #[\Override]
    public function getTools(?int $limit = null, ?string $cursor = null): Page
    {
        // 認証トークンが無い経路 (CLI 等) は絞らず内側の結果をそのまま返す。
        if (null === $this->tokenStorage->getToken()) {
            return $this->inner->getTools($limit, $cursor);
        }

        // ページング前に全件を取得して scope で絞る。 内側で先にページングしてから間引くと、
        // 可視 Tool が pageSize を跨いだとき空ページ + 非 null カーソルが生じ、 一覧が欠ける。
        // そこで絞った後の集合に対して自前で (内側と同じ base64(offset) 方式で) ページングする。
        $visible = array_values(array_filter(
            $this->inner->getTools()->references,
            fn ($tool): bool => $tool instanceof Tool && $this->isVisible($tool->name),
        ));

        if (null === $limit) {
            return new Page($visible, null);
        }

        $offset = $this->decodeCursor($cursor);
        $nextOffset = $offset + $limit;
        $nextCursor = $nextOffset < \count($visible) ? base64_encode((string) $nextOffset) : null;

        return new Page(array_slice($visible, $offset, $limit), $nextCursor);
    }

    /**
     * 現在のトークンがこの Tool を呼べるか。 中央マップ未登録 (= call 時 fail-closed deny) の Tool は
     * 一覧からも隠す。
     */
    private function isVisible(string $toolName): bool
    {
        $role = McpToolScopeMap::requiredRole($toolName);

        return null !== $role && $this->authorizationChecker->isGranted($role);
    }

    /**
     * 内側 Registry と同じ base64(offset) 形式のカーソルを offset へ復号する。 不正なカーソルは
     * 内側の `paginateResults` と同じく `InvalidCursorException` にする。
     */
    private function decodeCursor(?string $cursor): int
    {
        if (null === $cursor) {
            return 0;
        }

        $decoded = base64_decode($cursor, true);
        if (false === $decoded || !ctype_digit($decoded)) {
            throw new InvalidCursorException($cursor);
        }

        return (int) $decoded;
    }

    #[\Override]
    public function registerTool(Tool $tool, callable|array|string $handler): ToolReference
    {
        return $this->inner->registerTool($tool, $handler);
    }

    #[\Override]
    public function registerResource(ResourceDefinition $resource, callable|array|string $handler): ResourceReference
    {
        return $this->inner->registerResource($resource, $handler);
    }

    /**
     * @param array<string, class-string|object> $completionProviders
     */
    #[\Override]
    public function registerResourceTemplate(
        ResourceTemplate $template,
        callable|array|string $handler,
        array $completionProviders = [],
    ): ResourceTemplateReference {
        return $this->inner->registerResourceTemplate($template, $handler, $completionProviders);
    }

    /**
     * @param array<string, class-string|object> $completionProviders
     */
    #[\Override]
    public function registerPrompt(
        Prompt $prompt,
        callable|array|string $handler,
        array $completionProviders = [],
    ): PromptReference {
        return $this->inner->registerPrompt($prompt, $handler, $completionProviders);
    }

    #[\Override]
    public function unregisterTool(string $name): void
    {
        $this->inner->unregisterTool($name);
    }

    #[\Override]
    public function unregisterResource(string $uri): void
    {
        $this->inner->unregisterResource($uri);
    }

    #[\Override]
    public function unregisterResourceTemplate(string $uriTemplate): void
    {
        $this->inner->unregisterResourceTemplate($uriTemplate);
    }

    #[\Override]
    public function unregisterPrompt(string $name): void
    {
        $this->inner->unregisterPrompt($name);
    }

    #[\Override]
    public function hasTool(string $name): bool
    {
        return $this->inner->hasTool($name);
    }

    #[\Override]
    public function hasResource(string $uri): bool
    {
        return $this->inner->hasResource($uri);
    }

    #[\Override]
    public function hasResourceTemplate(string $uriTemplate): bool
    {
        return $this->inner->hasResourceTemplate($uriTemplate);
    }

    #[\Override]
    public function hasPrompt(string $name): bool
    {
        return $this->inner->hasPrompt($name);
    }

    #[\Override]
    public function hasTools(): bool
    {
        return $this->inner->hasTools();
    }

    #[\Override]
    public function getTool(string $name): ToolReference
    {
        return $this->inner->getTool($name);
    }

    #[\Override]
    public function hasResources(): bool
    {
        return $this->inner->hasResources();
    }

    #[\Override]
    public function getResources(?int $limit = null, ?string $cursor = null): Page
    {
        return $this->inner->getResources($limit, $cursor);
    }

    #[\Override]
    public function getResource(string $uri, bool $includeTemplates = true): ResourceReference|ResourceTemplateReference
    {
        return $this->inner->getResource($uri, $includeTemplates);
    }

    #[\Override]
    public function hasResourceTemplates(): bool
    {
        return $this->inner->hasResourceTemplates();
    }

    #[\Override]
    public function getResourceTemplates(?int $limit = null, ?string $cursor = null): Page
    {
        return $this->inner->getResourceTemplates($limit, $cursor);
    }

    #[\Override]
    public function getResourceTemplate(string $uriTemplate): ResourceTemplateReference
    {
        return $this->inner->getResourceTemplate($uriTemplate);
    }

    #[\Override]
    public function hasPrompts(): bool
    {
        return $this->inner->hasPrompts();
    }

    #[\Override]
    public function getPrompts(?int $limit = null, ?string $cursor = null): Page
    {
        return $this->inner->getPrompts($limit, $cursor);
    }

    #[\Override]
    public function getPrompt(string $name): PromptReference
    {
        return $this->inner->getPrompt($name);
    }
}
