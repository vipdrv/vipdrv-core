import { NgModule } from '@angular/core';
import { UsersComponent } from './users.component';
import { CommonModule } from '@angular/common';
import { NgaModule } from '../../theme/nga.module';
import { routing } from './users.routing';
import { UsersTableComponent } from './table/usersTable.component';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { UserDetailsEditComponent } from './details/edit/userDetailsEdit.component';
import { BusyModule } from 'angular2-busy';
import { FormsModule } from '@angular/forms';
import { TextMaskModule } from 'angular2-text-mask';
import { AppTranslationModule } from '../../app.translation.module';

@NgModule({
    imports: [
        CommonModule,
        NgaModule,
        routing,
        Ng2Bs3ModalModule,
        BusyModule,
        FormsModule,
        AppTranslationModule,
        TextMaskModule
    ],
    declarations: [
        UsersComponent,
        UsersTableComponent,
        UserDetailsEditComponent
    ],
    exports: [
        UsersComponent,
        UsersTableComponent,
        UserDetailsEditComponent
    ],
    providers: []
})
export class UsersModule {}
