import 'package:dio/dio.dart';

class ApiModule {
  static const String baseUrl = 'http://localhost:8000'; // API BASE URL

  static String? authToken = "";

  static void setAuthToken(String? token) {
    authToken = token;
  }

  static Future<SampleResponse> request(String url, String? method,
      {dynamic body, Map<String, dynamic>? headers}) async {
    final token = ApiModule.authToken;

    method = method ?? 'GET';
    method = method.toUpperCase();
    
    body = body ?? {};

    if (url.startsWith('/')) {
      url = url.substring(1);
    }

    if (!url.startsWith('http')) {
      url = '$baseUrl/$url';
    }

    headers ??= {};
    headers['Content-Type'] = 'application/json';

    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }

    final dio = Dio();
    dio.options.headers = headers;
    Response response;
    try {
      if (method == 'GET') {
        response = await dio.get(url);
      } else if (method == 'POST') {
        response = await dio.post(url, data: body);
      } else if (method == 'PUT') {
        response = await dio.put(url, data: body);
      } else if (method == 'DELETE') {
        response = await dio.delete(url);
      } else {
        throw Exception('Invalid HTTP method: $method');
      }

      SampleResponse sampleResponse = SampleResponse(response.statusCode ?? 0, response.data);

      return sampleResponse;
    } catch (error) {
      if (error is DioException) {
          // print(error.response?.data);
          dynamic data = error.response?.data;
          if (data.isNotEmpty) {
            return SampleResponse(error.response?.statusCode ?? 500, data);
          }
      } else {
        // print('Error: ${error.toString()}');
      }
    }
    return SampleResponse(0, 'LOCAL: Inconnu');
  }
}

class SampleResponse {
  int statusCode;
  dynamic data;

  SampleResponse(this.statusCode, this.data);
}