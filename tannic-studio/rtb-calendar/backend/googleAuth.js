// Backend/googleAuth.js
// Generates OAuth2 access tokens for Google Sheets API using a service account.
// Prerequisites:
//   1. Install "jsonwebtoken" via Wix Package Manager (Sidebar > Packages > npm)
//   2. Add these secrets in Wix Dashboard > Developer Tools > Secrets Manager:
//      - "google-sheets-credentials" → paste the full service account JSON file content
//      - "google-sheets-id" → the spreadsheet ID from your Google Sheet URL

import { getSecret } from 'wix-secrets-backend';
import { fetch } from 'wix-fetch';
import jwt from 'jsonwebtoken';

const SCOPES = 'https://www.googleapis.com/auth/spreadsheets.readonly';
const TOKEN_URL = 'https://oauth2.googleapis.com/token';

export async function getAccessToken() {
  const credsJson = await getSecret('google-sheets-credentials');
  const creds = JSON.parse(credsJson);

  const now = Math.floor(Date.now() / 1000);

  const payload = {
    iss: creds.client_email,
    scope: SCOPES,
    aud: TOKEN_URL,
    iat: now,
    exp: now + 3600
  };

  const signedJwt = jwt.sign(payload, creds.private_key, { algorithm: 'RS256' });

  const response = await fetch(TOKEN_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer&assertion=${signedJwt}`
  });

  const data = await response.json();

  if (!data.access_token) {
    throw new Error(`Google auth failed: ${JSON.stringify(data)}`);
  }

  return data.access_token;
}
