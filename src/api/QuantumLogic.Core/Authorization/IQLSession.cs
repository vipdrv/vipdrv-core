﻿using System.Collections.Generic;

namespace QuantumLogic.Core.Authorization
{
    /// <summary>
    /// Is used as session for QuantumLogic application
    /// </summary>
    public interface IQLSession
    {
        int? UserId { get; set; }
        string Username { get; }
        ISet<string> GrantedPermissions { get; }
    }
}
