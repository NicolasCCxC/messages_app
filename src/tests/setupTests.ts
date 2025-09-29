import '@testing-library/jest-dom';

import { TextEncoder, TextDecoder } from 'util';

process.env.VITE_BASE_URL = 'http://localhost:5174/mock';

if (!(global as any).TextEncoder) {
  (global as any).TextEncoder = TextEncoder;
}
if (!(global as any).TextDecoder) {
  (global as any).TextDecoder = TextDecoder as unknown as typeof globalThis.TextDecoder;
}

try {
  if (!(global as any).crypto) {
    const { webcrypto } = require('crypto');
    (global as any).crypto = webcrypto;
  }
} catch {}

jest.mock('@api/FetchClient');

afterEach(() => {
  jest.clearAllMocks();
});
