const { encrypt } = require('cs2-encryption');
const { writeFile, readFile } = require('fs').promises;

async function main() {
  // Parse the input parameters
  const args = JSON.parse(await readFile(process.argv[2], 'utf-8'));

  const context = args.capture_context;
  const data = {
    number: args.cc,
    expirationMonth: args.mm,
    expirationYear: args.yyyy,
    type: args.type,
  };

  const encrypted = await encrypt(data, context);
  await writeFile(args.joanna, JSON.stringify(encrypted, null, 2));
}

main();
