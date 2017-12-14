import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { BusyModule } from 'angular2-busy';
import { UtilsModule } from './../../utils/index';
import { UserProfileComponent } from './userProfile.component';
import { PersonalInfoComponent } from './personalInfo/personalInfo.component';
import { PasswordUpdateComponent } from './passwordUpdate/passwordUpdate.component';
import { AvatarUpdateComponent } from './avatarUpdate/avatarUpdate.component';
import { routing } from './userProfile.routing';
import { TextMaskModule } from 'angular2-text-mask';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        Ng2Bs3ModalModule,
        BusyModule,
        UtilsModule,
        TextMaskModule
    ],
    exports: [
        UserProfileComponent,
        PersonalInfoComponent,
        AvatarUpdateComponent,
        PasswordUpdateComponent,
    ],
    declarations: [
        UserProfileComponent,
        PersonalInfoComponent,
        AvatarUpdateComponent,
        PasswordUpdateComponent,
    ],
    providers: [],
})
export class UserProfileModule {}
