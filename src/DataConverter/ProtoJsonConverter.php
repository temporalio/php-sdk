<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\DataConverter;

use Google\Protobuf\Internal\Message;
use Temporal\Exception\DataConverterException;

class ProtoJsonConverter implements PayloadConverterInterface
{
    /**
     * @return string
     */
    public function getEncodingType(): string
    {
        return EncodingKeys::METADATA_ENCODING_PROTOBUF_JSON;
    }

    /**
     * @param mixed $value
     * @return Payload|null
     */
    public function toPayload($value): ?Payload
    {
        if (!$value instanceof Message) {
            return null;
        }

        return Payload::create(
            [EncodingKeys::METADATA_ENCODING_KEY => EncodingKeys::METADATA_ENCODING_PROTOBUF_JSON],
            $value->serializeToJsonString()
        );
    }

    /**
     * @param Payload $payload
     * @param \ReflectionType|null $type
     * @return Message
     * @throws DataConverterException
     */
    public function fromPayload(Payload $payload, ?\ReflectionType $type)
    {
        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            try {
                $obj = new \ReflectionClass($type->getName());
            } catch (\ReflectionException $e) {
                throw new DataConverterException($e->getMessage(), $e->getCode(), $e);
            }

            /** @var Message $instance */
            $instance = $obj->newInstance();
            $instance->mergeFromJsonString($payload->getData());

            return $instance;
        } else {
            throw new DataConverterException("Unable to decode value using protobuf converter");
        }
    }
}
