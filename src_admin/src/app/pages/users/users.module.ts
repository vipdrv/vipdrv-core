import { NgModule } from '@angular/core';
import { UsersComponent } from './users.component';
import { CommonModule } from '@angular/common';
import { NgaModule } from '../../theme/nga.module';
import { routing } from './users.routing';
import { UsersTableComponent } from './table/usersTable.component';

@NgModule({
    imports: [
        CommonModule,
        NgaModule,
        routing,

    ],
    declarations: [
        UsersComponent,
        UsersTableComponent
    ],
    exports: [
        UsersComponent,
        UsersTableComponent
    ],
    providers: []
})
export class UsersModule {
}
