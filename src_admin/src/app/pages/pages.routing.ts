import { Routes, RouterModule }  from '@angular/router';
import { Pages } from './pages.component';
import { ModuleWithProviders } from '@angular/core';
import { AuthorizationService } from './../services/index';

// noinspection TypeScriptValidateTypes

// export function loadChildren(path) { return System.import(path); };

export const routes: Routes = [
    {
        path: 'login',
        loadChildren: './login/login.module#LoginModule'
    },
    {
        path: 'logout',
        loadChildren: './logout/logout.module#LogoutModule'
    },
    {
        path: 'registration',
        loadChildren: './registration/registration.module#RegistrationModule'
    },
    {
        path: 'pages',
        component: Pages,
        children: [
            {
                path: '',
                redirectTo: 'home',
                pathMatch: 'full',
                canActivate: [AuthorizationService]
            },
            {
                path: 'home',
                loadChildren: './home/home.module#HomeModule',
                canActivate: [AuthorizationService]
            },
            {
                path: 'leads',
                loadChildren: './leads/leads.module#LeadsModule',
                canActivate: [AuthorizationService]
            },
            {
                path: 'sites',
                loadChildren: './sites/sites.module#SitesModule',
                canActivate: [AuthorizationService]
            },
            {
                path: 'settings',
                loadChildren: './settings/settings.module#SettingsModule',
                canActivate: [AuthorizationService]
            },
            {
                path: 'settings/invitations',
                loadChildren: './invitations/invitations.module#InvitationsModule',
                canActivate: [AuthorizationService]
            },
            {
                path: 'user-profile',
                loadChildren: './userProfile/userProfile.module#UserProfileModule',
                canActivate: [AuthorizationService]
            },
            { path: 'test', loadChildren: './test/test.module#TestModule', canActivate: [AuthorizationService] }
        ]
    }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);
