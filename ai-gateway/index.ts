import { config } from 'dotenv';
import { gateway, streamText } from 'ai';

config({ path: '.env.local' });

const result = streamText({
  model: gateway('openai/gpt-5.4'),
  prompt: 'Tulis satu haiku tentang coding.',
});

process.stdout.write('\n');
for await (const part of result.textStream) {
  process.stdout.write(part);
}
process.stdout.write('\n');

const usage = await result.usage;
console.log('\nToken usage:', {
  input: usage.inputTokens,
  output: usage.outputTokens,
  total: usage.totalTokens,
});