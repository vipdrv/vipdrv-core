import { RouterModule, Routes } from '@angular/router';
import { UsersComponent } from './users.component';
import { ModuleWithProviders } from '@angular/core';

export const routes: Routes = [
    {
        path: '',
        component: UsersComponent,
        children: [],
    },
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);
