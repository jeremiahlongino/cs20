const fs   = require('fs');
const path = require('path');
const readline = require('readline');
const { MongoClient } = require('mongodb');

const MONGO_URI  = process.env.MONGO_URI || 'mongodb+srv://jeremiahlongino04_db_user:BUQ1aKnXXdJmLIzJ@publiccompanies.ys5yjlh.mongodb.net/';
const DB_NAME    = 'Stock';
const COLLECTION = 'PublicCompanies';
const CSV_FILE   = path.join(__dirname, '..', 'companies-1.csv');

async function main() {
  const client = new MongoClient(MONGO_URI);

  try {
    await client.connect();

    const db  = client.db(DB_NAME);
    const col = db.collection(COLLECTION);

    const rl = readline.createInterface({
      input: fs.createReadStream(CSV_FILE),
      crlfDelay: Infinity,
    });

    let lineNumber = 0;

    for await (const line of rl) {
      lineNumber++;

      // skips the header row
      if (lineNumber === 1) {
        continue;
      }

      // skips any blank lines
      if (!line.trim()) continue;

      // Needs company, ticker, and price
      const parts = line.split(',');
      if (parts.length < 3) {
        console.warn(`⚠️   Line ${lineNumber} has fewer than 3 fields – skipping: ${line}`);
        continue;
      }

      // handles if name has commas
      const price   = parseFloat(parts[parts.length - 1].trim());
      const ticker  = parts[parts.length - 2].trim();
      const company = parts.slice(0, parts.length - 2).join(',').trim();

      const doc = { company, ticker, price };
      console.log(`   Inserting line ${lineNumber}:`, doc);

      await col.insertOne(doc);
    }

    console.log(`${lineNumber - 1} record(s) inserted into ${DB_NAME}.${COLLECTION}`);
  } catch (err) {
    console.error('Womp Womp:', err.message);
  } finally {
    await client.close();
  }
}

main();