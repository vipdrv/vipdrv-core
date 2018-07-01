using System.Collections.Generic;
using System.Linq;

namespace QuantumLogic.Core.Extensions
{
    public static partial class QuantumLogicExtensions
    {
        /// <summary>
        /// Is used to replase all oldElem to new Elem via ReferenceEquals
        /// </summary>
        /// <typeparam name="TElement">type of element</typeparam>
        /// <param name="collection">collection</param>
        /// <param name="old">old element</param>
        /// <param name="new">new element</param>
        /// <returns>count of replacements</returns>
        public static int ReplaceAllReference<TElement>(this ICollection<TElement> collection, TElement old, TElement @new)
        {
            int count = 0;
            while (true)
            {
                var elem = collection.FirstOrDefault(r => ReferenceEquals(r, old));
                if (elem != null)
                {
                    collection.Remove(elem);
                    collection.Add(@new);
                    count++;
                }
                else
                {
                    break;
                }
            };
            return count;
        }

        /// <summary>
        /// Is used to modify collection to be merged with other collection
        /// </summary>
        /// <typeparam name="T">type of element</typeparam>
        /// <param name="collention"></param>
        /// <param name="otherCollection"></param>
        public static void Merge<T>(this ICollection<T> collention, ICollection<T> otherCollection)
        {
            IEnumerable<T> deleteCandidates = collention.Except(otherCollection);
            IEnumerable<T> addCandidates = otherCollection.Except(collention);
            foreach (var deleteCandidate in deleteCandidates)
            {
                collention.Remove(deleteCandidate);
            }
            foreach (var addCandidate in addCandidates)
            {
                collention.Add(addCandidate);
            }
        }
    }
}
