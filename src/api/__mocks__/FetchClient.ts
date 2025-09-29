type Data = unknown;
type Options = unknown;

export type MockFetchClient = {
  get: jest.Mock<Promise<any>, [string, Options?]>;
  post: jest.Mock<Promise<any>, [string, Data?, Options?]>;
  patch: jest.Mock<Promise<any>, [string, Data?, Options?]>;
  delete: jest.Mock<Promise<any>, [string, Options?]>;
  setAuthToken: jest.Mock<void, [string | null]>;
};

const FetchClient: MockFetchClient = {
  get: jest.fn(),
  post: jest.fn(),
  patch: jest.fn(),
  delete: jest.fn(),
  setAuthToken: jest.fn(),
};

export default FetchClient;

export const __setAllResolved = (value: any) => {
  FetchClient.get.mockResolvedValue(value);
  FetchClient.post.mockResolvedValue(value);
  FetchClient.patch.mockResolvedValue(value);
  FetchClient.delete.mockResolvedValue(value);
};

export const __setAllRejected = (error: any) => {
  FetchClient.get.mockRejectedValue(error);
  FetchClient.post.mockRejectedValue(error);
  FetchClient.patch.mockRejectedValue(error);
  FetchClient.delete.mockRejectedValue(error);
};
