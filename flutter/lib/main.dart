import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../authprovider.dart';
import '../apimodule.dart';
import '../providers/user.dart';
import '../models/user.dart';
import 'dart:async';
import 'package:untitled2/menu/home_screen.dart';
import 'package:untitled2/menu/ressources_screen.dart';
import 'package:untitled2/menu/login_screen.dart';
import 'package:untitled2/menu/inscription_screen.dart';
// import 'package:untitled2/menu/create_ressource.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider<AuthProvider>(create: (_) => AuthProvider()),
        ChangeNotifierProvider<UserProvider>(create: (_) => UserProvider()),
      ],
      child: const MyApp(),
    ),
  );
}

Color hexToColor(String hexString, {double opacity = 1}) {
  final buffer = StringBuffer();
  if (hexString.length == 6 || hexString.length == 7) buffer.write('ff');
  buffer.write(hexString.replaceFirst('#', ''));
  return Color(int.parse(buffer.toString(), radix: 16)).withOpacity(opacity);
}

MaterialColor createMaterialColor(Color color) {
  List strengths = <double>[.05];
  Map<int, Color> swatch = <int, Color>{};
  final int r = color.red, g = color.green, b = color.blue;

  for (int i = 1; i < 10; i++) {
    strengths.add(0.1 * i);
  }

  for (var strength in strengths) {
    final double ds = 0.5 - strength;
    swatch[(strength * 1000).round()] = Color.fromRGBO(
      r + ((ds < 0 ? r : (255 - r)) * ds).round(),
      g + ((ds < 0 ? g : (255 - g)) * ds).round(),
      b + ((ds < 0 ? b : (255 - b)) * ds).round(),
      1,
    );
  }

  return MaterialColor(color.value, swatch);
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Ressources relationnelles',
      theme: ThemeData(
        primarySwatch: createMaterialColor(hexToColor('#eaf3ec')),
      ),
      home: const MyHomePage(title: 'Ressources relationnelles'),
    );
  }
}

class MyHomePage extends StatefulWidget {
  const MyHomePage({Key? key, required this.title}) : super(key: key);
  final String title;

  @override
  MyHomePageState createState() => MyHomePageState();
}

class MyHomePageState extends State<MyHomePage> {
  bool get estConnecte => Provider.of<AuthProvider>(context).token != null;

Future<bool> seConnecter(String nomUtilisateur, String motDePasse) async {
  final authProvider = Provider.of<AuthProvider>(context, listen: false);
  
  final loginResponse = await ApiModule.request('api/login_check', 'POST',
      body: {'username': nomUtilisateur, 'password': motDePasse});

  if (loginResponse.statusCode == 200) {
    final responseJson = loginResponse.data;
    final token = responseJson['token'];

    authProvider.setToken(token);
    ApiModule.setAuthToken(token);
    final userResponse = await ApiModule.request('api/citoyen', 'GET');
    if (userResponse.statusCode == 200) {
      awaitConnect(User.fromJson(userResponse.data));
    }

    return true;
  } else {
    return false;
  }
}

void awaitConnect(user) async {
      final userProvider = Provider.of<UserProvider>(context, listen: false);
      userProvider.setUser(user);
}

  void seDeconnecter() {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    authProvider.setToken(null);
  }

  int _currentIndex = 0;
  final List<Widget> _screens = [
    const HomeScreen(),
    const RessourcesScreen(),
    // CreateRessourcesComponent(),
  ];

  Widget _buildDrawerHeader(User? user) {
    if (user == null) {
      return const UserAccountsDrawerHeader(
        accountName: Text(''),
        accountEmail: null,
        currentAccountPicture: CircleAvatar(
          child: Icon(Icons.person),
        ),
      );
    }

    return UserAccountsDrawerHeader(
      accountName: Text('${user.nom} ${user.prenom}'),
      accountEmail: null,
      currentAccountPicture: const CircleAvatar(
        child: Icon(Icons.person),
      ),
    );
  }


  @override
  Widget build(BuildContext context) {
    final userProvider = Provider.of<UserProvider>(context);
    // final user = userProvider.getUser();

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        actions: estConnecte
            ? [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () {
              seDeconnecter();
            },
          ),
        ]
            : [
          IconButton(
            icon: const Icon(Icons.person_add),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const InscriptionScreen()),
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.login),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => LoginScreen(onLogin: seConnecter)),
              );
            },
          ),
        ],
      ),
      drawer: Drawer(
        child: ListView(
          padding: EdgeInsets.zero,
          children: <Widget>[
            _buildDrawerHeader(userProvider.getUser()),
            ListTile(
              title: const Text('Fil d\'actualité'),
              onTap: () {
                setState(() {
                  _currentIndex = 0;
                });
                Navigator.pop(context);
              },
            ),
            ListTile(
              title: const Text('Recherche de ressources'),
              onTap: () {
                setState(() {
                  _currentIndex = 1;
                });
                Navigator.pop(context);
              },
            ),
            // ListTile(
            //   title: const Text('Ajout d\'une ressource'),
            //   onTap: () {
            //     setState(() {
            //       _currentIndex = 2;
            //     });
            //     Navigator.pop(context);
            //   },
            // ),
          ],
        ),
      ),
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
    );
  }
}
