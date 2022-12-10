<?php

namespace Ensi\LaravelPrometheus\Metrics;

use Ensi\LaravelPrometheus\MetricsBag;
use Prometheus\Summary as LowLevelSummary;

class Summary extends AbstractMetric
{
    private ?LowLevelSummary $summary = null;

    public function __construct(
        protected MetricsBag $metricsBag,
        private string $name,
        private int $maxAgeSeconds,
        private array $quantiles,
    ) {
    }

    public function update($value = 1, array $labelValues = []): void
    {
        $this->getSummary()->observe(
            $value,
            $this->enrichLabelValues($labelValues)
        );
    }

    private function getSummary(): LowLevelSummary
    {
        if (!$this->summary) {
            $this->summary = $this->metricsBag->getCollectors()->registerSummary(
                $this->metricsBag->getNamespace(),
                $this->name,
                $this->help,
                $this->enrichLabelNames($this->labels),
                $this->maxAgeSeconds,
                $this->quantiles,
            );
        }

        return $this->summary;
    }
}