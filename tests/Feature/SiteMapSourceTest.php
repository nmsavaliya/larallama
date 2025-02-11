<?php

namespace Tests\Feature;

use App\Domains\Sources\FeedSource\FeedItemDto;
use App\Domains\Sources\SourceTypeEnum;
use App\Models\Source;
use Facades\App\Domains\Sources\SiteMapSource\SiteMapParserWrapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SiteMapSourceTest extends TestCase
{
    public function test_run()
    {

        Bus::fake();

        $data = get_fixture('sitemap_parsed_results.json');

        $item = Arr::first($data);

        $item = FeedItemDto::from($item);

        SiteMapParserWrapper::shouldReceive('handle')
            ->once()->andReturn(
                collect([$item])
            );

        $source = Source::factory()->create([
            'slug' => 'test',
            'type' => SourceTypeEnum::SiteMapSource,
            'meta_data' => [
                'feed_url' => 'https://firehouse.agency/sitemap.xml',
            ],
        ]);

        $source->run();

        Bus::assertBatchCount(1);
    }
}
