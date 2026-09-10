<?php

use Eazpl\Utils\Utils;

it('wraps UTF-8 text without duplicating explicit line breaks', function () {
    expect(Utils::utf8Wordwrap("AA\nBBBB", 2, "\n", true))->toBe("AA\nBB\nBB");
});

it('cuts long UTF-8 words at the requested width', function () {
    expect(Utils::utf8Wordwrap('ABCDEFG', 3, "\n", true))->toBe("ABC\nDEF\nG");
});

it('counts UTF-8 characters instead of bytes while wrapping', function () {
    expect(Utils::utf8Wordwrap('سلام دنیا', 4, "\n", true))->toBe("سلام\nدنیا");
});
