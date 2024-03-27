import 'package:flutter/material.dart';
import '../apimodule.dart';
import 'ressource_screen.dart'; 
import '../models/ressource.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  HomeScreenState createState() => HomeScreenState();
}

class HomeScreenState extends State<HomeScreen> {
  List<Ressource> resources = [];
  int currentPage = 1;
  bool isLoading = false;
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    fetchResources();
    _scrollController.addListener(_scrollListener);
  }

  @override
  void dispose() {
    _scrollController.removeListener(_scrollListener);
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollListener() {
    if (_scrollController.offset >=
        _scrollController.position.maxScrollExtent &&
        !_scrollController.position.outOfRange) {
      if (!isLoading) {
        fetchResources();
      }
    }
  }

  Future<void> fetchResources() async {
    try {
      setState(() {
        isLoading = true;
      });

      final response = await ApiModule.request(
          '/api/ressources?order[dateCreation]=des&page=$currentPage', 'GET');
      if (response.statusCode == 200) {
        final jsonData = response.data;
        final List<dynamic> resourcesData = jsonData['hydra:member'];
        setState(() {
          resources.addAll(resourcesData
              .map((data) => Ressource.fromJson(data))
              .toList());
          currentPage++;
          isLoading = false;
        });
      } else {
        // print('Error: ${response.statusCode}');
        setState(() {
          isLoading = false;
        });
      }
    } catch (e) {
      // print('Exception: $e');
      setState(() {
        isLoading = false;
      });
    }
  }

  Widget _buildResourceCard(Ressource resource) {
    return Card(
      child: Column(
        children: [
          ListTile(
            title: Text(
              resource.titre,
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            subtitle: Text(resource.texte),
            trailing: Text(resource.dateCreation),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Row(
              children: [
                const CircleAvatar(
                  backgroundImage: AssetImage('images/avatar.jpg'),
                ),
                const SizedBox(width: 8),
                Text('${resource.createur.prenom} ${resource.createur.nom}'),
              ],
            ),
          ),
          ButtonBar(
            children: [
              ElevatedButton(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => RessourceScreen(ressourceId: resource.id),
                    ),
                  );
                },
                child: const Text('En savoir plus'),
              ),
              IconButton(
                onPressed: () {
                },
                icon: const Icon(Icons.favorite),
                tooltip: '${resource.nbFavoris} mis en favoris',
              ),
              IconButton(
                onPressed: () {
                },
                icon: const Icon(Icons.comment),
                tooltip: '${resource.nbCommentaires} commentaires',
              ),
            ],
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      // appBar: AppBar(
      //   title: Text('Accueil'),
      // ),
      body: NotificationListener<ScrollNotification>(
        onNotification: (scrollNotification) {
          if (scrollNotification is ScrollEndNotification) {
            if (_scrollController.position.extentAfter == 0) {
              fetchResources();
            }
          }
          return false;
        },
        child: ListView.builder(
          controller: _scrollController,
          itemCount: resources.length + 1,
          itemBuilder: (context, index) {
            if (index < resources.length) {
              final resource = resources[index];
              return _buildResourceCard(resource);
            }
            if (isLoading) {
              return const Center(
                child: CircularProgressIndicator(),
              );
            }
            return const SizedBox();
          },
        ),
      ),
    );
  }
}