import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';
import { SettingsComponent } from './settings.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: SettingsComponent,
        children: []
    },
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);