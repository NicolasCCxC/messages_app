import type { Config } from 'jest';
import { pathsToModuleNameMapper } from 'ts-jest';

const { compilerOptions } = require('./tsconfig.json');

const config: Config = {
  testEnvironment: 'jsdom',
  setupFilesAfterEnv: ['<rootDir>/src/tests/setupTests.ts'],

  transform: {
    '^.+\\.(t|j)sx?$': [
      'ts-jest',
      { tsconfig: '<rootDir>/tsconfig.jest.json', isolatedModules: true }
    ]
  },

  moduleFileExtensions: ['ts', 'tsx', 'js', 'jsx'],

  moduleNameMapper: {
    ...(compilerOptions?.paths
      ? pathsToModuleNameMapper(compilerOptions.paths, { prefix: '<rootDir>/' })
      : {}),

      '^@components/(.*)$': '<rootDir>/src/components/$1',
      '^@constants/(.*)$':  '<rootDir>/src/constants/$1',
      '^@hooks/(.*)$': '<rootDir>/src/hooks/$1',
      '^@redux/(.*)$': '<rootDir>/src/redux/$1',
      '^@utils/(.*)$': '<rootDir>/src/utils/$1',
      '^@models/(.*)$': '<rootDir>/src/models/$1',
      '^@pages/(.*)$': '<rootDir>/src/pages/$1',
      '^@api/(.*)$': '<rootDir>/src/api/$1',
    
      '^.+\\.module\\.(css|scss|sass)$': 'identity-obj-proxy',
      '^.+\\.(css|scss|sass)$': '<rootDir>/src/tests/styleMock.ts',
      '\\.(jpg|jpeg|png|gif|webp|svg)$': '<rootDir>/src/tests/fileMock.ts',
  },

  collectCoverageFrom: [
    'src/**/*.{ts,tsx}',
    '!src/**/*.test.{ts,tsx}',
    '!src/**/__tests__/**',
    '!src/**/*.d.ts',
    '!src/**/index.{ts,tsx}',
    '!src/**/*.(stories|story).{ts,tsx}',
    '!src/**/__mocks__/**',
    '!src/tests/**',
    '!src/tests/setupTests.ts',
    '!src/**/mocks/**',
  ],
  clearMocks: true,
  reporters: [
    'default',
    ['jest-junit', { outputDirectory: 'reports', outputName: 'junit.xml' }],
  ],
  collectCoverage: true,
  coverageReporters: ['lcov', 'text-summary'],
  coverageDirectory: 'coverage',
  transformIgnorePatterns: ['/node_modules/'],
  //coverageThreshold: {
  //  global: {
  //    statements: 82,
  //    branches:   82,
  //    functions:  82,
  //    lines:      82,
  //  },
  //},
};

export default config;
