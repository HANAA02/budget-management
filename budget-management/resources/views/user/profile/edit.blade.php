@extends('layouts.user')

@section('title', 'Modifier mon profil')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Modifier mon profil</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-3 mb-4 text-center">
                                <div class="avatar-upload">
                                    <div class="avatar-preview">
                                        <div id="imagePreview" style="background-image: url('{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/default-avatar.png') }}');">
                                        </div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type='file' id="avatar" name="avatar" accept=".png, .jpg, .jpeg" />
                                        <label for="avatar">
                                            <i class="fa fa-pencil-alt"></i>
                                        </label>
                                    </div>
                                </div>
                                <p class="small text-muted mt-2">Cliquez sur l'image pour la modifier</p>
                            </div>
                            
                            <div class="col-md-9">
                                <div class="form-group row mb-3">
                                    <label for="nom" class="col-md-3 col-form-label">Nom</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                        @error('nom')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-3">
                                    <label for="prenom" class="col-md-3 col-form-label">Prénom</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                        @error('prenom')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-3">
                                    <label for="email" class="col-md-3 col-form-label">Email</label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="form-group row mb-3">
                                    <label for="devise_defaut" class="col-md-3 col-form-label">Devise par défaut</label>
                                    <div class="col-md-9">
                                        <select class="form-control @error('devise_defaut') is-invalid @enderror" id="devise_defaut" name="devise_defaut">
                                            <option value="MAD" {{ old('devise_defaut', $user->devise_defaut) == 'MAD' ? 'selected' : '' }}>Dirham marocain (MAD)</option>
                                            <option value="EUR" {{ old('devise_defaut', $user->devise_defaut) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                            <option value="USD" {{ old('devise_defaut', $user->devise_defaut) == 'USD' ? 'selected' : '' }}>Dollar américain (USD)</option>
                                            <option value="GBP" {{ old('devise_defaut', $user->devise_defaut) == 'GBP' ? 'selected' : '' }}>Livre sterling (GBP)</option>
                                        </select>
                                        @error('devise_defaut')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group row mb-3">
                                    <label for="theme" class="col-md-3 col-form-label">Thème</label>
                                    <div class="col-md-9">
                                        <select class="form-control @error('theme') is-invalid @enderror" id="theme" name="theme">
                                            <option value="light" {{ old('theme', $user->theme) == 'light' ? 'selected' : '' }}>Clair</option>
                                            <option value="dark" {{ old('theme', $user->theme) == 'dark' ? 'selected' : '' }}>Sombre</option>
                                        </select>
                                        @error('theme')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group row mb-3">
                            <label for="current_password" class="col-md-3 col-form-label">Mot de passe actuel</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                <small class="form-text text-muted">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</small>
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-3 col-form-label">Nouveau mot de passe</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="password_confirmation" class="col-md-3 col-form-label">Confirmer le mot de passe</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <h5>Préférences de notification</h5>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notification_email" name="notification_email" value="1" {{ old('notification_email', $user->notification_email) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notification_email">Recevoir des notifications par email</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notification_alerte_budget" name="notification_alerte_budget" value="1" {{ old('notification_alerte_budget', $user->notification_alerte_budget) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notification_alerte_budget">Alertes de dépassement de budget</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notification_objectif_atteint" name="notification_objectif_atteint" value="1" {{ old('notification_objectif_atteint', $user->notification_objectif_atteint) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notification_objectif_atteint">Notifications d'objectif atteint</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="notification_rappel_revenu" name="notification_rappel_revenu" value="1" {{ old('notification_rappel_revenu', $user->notification_rappel_revenu) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notification_rappel_revenu">Rappels de revenus à venir</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Mettre à jour
                                </button>
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Prévisualisation de l'avatar
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        $("#avatar").change(function() {
            readURL(this);
        });
    });
</script>
@endsection
