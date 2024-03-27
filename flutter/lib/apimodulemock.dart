
abstract class ApiModule {

  // Abstract method to be implemented in subclasses
  Future<SampleResponse> request(String? url, String? method,
      {dynamic body, Map<String, dynamic>? headers});
}

class SampleResponse {
  int statusCode;
  dynamic data;

  SampleResponse(this.statusCode, this.data);
}