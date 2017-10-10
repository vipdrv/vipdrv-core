import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';
import { AuthorizationManager } from './utils/index';

export const routes: Routes = [
  { path: '', redirectTo: 'pages', pathMatch: 'full' },
  { path: '**', redirectTo: 'pages/home', canActivate: [AuthorizationManager] },
];

export const routing: ModuleWithProviders = RouterModule.forRoot(routes, { useHash: true });