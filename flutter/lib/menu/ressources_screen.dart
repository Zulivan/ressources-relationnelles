import 'package:flutter/material.dart';
import '../apimodule.dart';
import 'ressource_screen.dart'; 
import '../models/ressource.dart';
import '../models/categorie.dart';
import '../models/type_ressource.dart';

class RessourcesScreen extends StatefulWidget {
  const RessourcesScreen({super.key});

  @override
  RessourcesScreenState createState() => RessourcesScreenState();
}

class RessourcesScreenState extends State<RessourcesScreen> {
  String categorieSelectionnee = '';
  String typeSelectionne = '';
  bool validee = true;
  int page = 1;
  List<Categorie> categories = [];
  List<TypeRessource> typesRessources = [];
  List<Ressource> ressources = [];
  bool isLoading = false;
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    getCategories();
    getTypesRessources();
    getRessources();
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
          page++;
        getRessources();
      }
    }
  }

  Future<void> getCategories() async {
    try {
      const url = 'api/categories';
      final response = await ApiModule.request(url, 'GET');
      if (response.statusCode == 200) {
        final jsonData = response.data;
        // print(jsonData);
        setState(() {
          categories = (jsonData['hydra:member'] as List)
              .map((data) => Categorie.fromJson(data))
              .toList();
        });
      } else {
        // print('Error: ${response.statusCode}');
      }
    } catch (e) {
      // print('Exception: $e');
    }
  }

  Future<void> getTypesRessources() async {
    try {
      const url = 'api/type_ressources';
      final response = await ApiModule.request(url, 'GET');
      if (response.statusCode == 200) {
        final jsonData = response.data;
        setState(() {
          typesRessources = (jsonData['hydra:member'] as List)
              .map((data) => TypeRessource.fromJson(data))
              .toList();
        });
      } else {
        // print('Error: ${response.statusCode}');
      }
    } catch (e) {
      // print('Exception: $e');
    }
  }

  void onSelectChange(value) {
    // print(value);
    setState(() {
      categorieSelectionnee = value;
    });
    page = 1;
    getRessources();
  }

  Future<void> getRessources() async {
    try {
      setState(() {
        isLoading = true;
      });



      final url = 'api/ressources?page=$page&categories.id=$categorieSelectionnee&typeRessource.id=$typeSelectionne&validee=$validee';
      final response = await ApiModule.request(url, 'GET');
      if (response.statusCode == 200) {
        final jsonData = response.data;
        final List<dynamic> ressourcesData = jsonData['hydra:member'];
        
        if(page==1){
          ressources.clear();
        }
      
        setState(() {
          ressources.addAll(ressourcesData
              .map((data) => Ressource.fromJson(data))
              .toList());
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
                onPressed: () {},
                icon: const Icon(Icons.favorite),
                tooltip: '${resource.nbFavoris} mis en favoris',
              ),
              IconButton(
                onPressed: () {},
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
      body: Column(
        children: [
          // Dropdown for selecting categories
          if (categories.isNotEmpty)
            DropdownButton<String>(
              value: categorieSelectionnee==""?null:categorieSelectionnee,
              onChanged: onSelectChange,
              items: categories.map<DropdownMenuItem<String>>((Categorie category) {
                return DropdownMenuItem<String>(
                  value: category.id.toString(),
                  child: Text(category.libelle),
                );
              }).toList(),
            ),
          // Dropdown for selecting types
          if (typesRessources.isNotEmpty)
            DropdownButton<String>(
              value: typeSelectionne==""?null:typeSelectionne,
              onChanged: (value) {
                setState(() {
                  typeSelectionne = value??'-1';
                });
                page = 1;
                getRessources();
              },
              items: typesRessources.map<DropdownMenuItem<String>>((TypeRessource type) {
                return DropdownMenuItem<String>(
                  value: type.id.toString(),
                  child: Text(type.libelle),
                );
              }).toList(),
            ),

          // Display the list of resources
          Expanded(
            child: NotificationListener<ScrollNotification>(
              onNotification: (scrollNotification) {
                if (scrollNotification is ScrollEndNotification) {
                  if (_scrollController.position.extentAfter == 0) {
                    getRessources();
                  }
                }
                return false;
              },
              child: ListView.builder(
                controller: _scrollController,
                itemCount: ressources.length + 1,
                itemBuilder: (context, index) {
                  if (index < ressources.length) {
                    final resource = ressources[index];
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
          ),
        ],
      ),
    );
  }
}